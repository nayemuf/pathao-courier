<?php

namespace Nayemuf\PathaoCourier\Apis;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Nayemuf\PathaoCourier\Exceptions\PathaoException;

abstract class BaseApi
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var bool
     */
    protected $sandbox;

    /**
     * Rate limiting: max requests per minute
     */
    protected const RATE_LIMIT_PER_MINUTE = 60;

    /**
     * Cache key prefix
     */
    protected const CACHE_PREFIX = 'pathao_courier_';

    /**
     * BaseApi constructor.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $username
     * @param string $password
     * @param bool $sandbox
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        string $username,
        string $password,
        bool $sandbox = false
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->username = $username;
        $this->password = $password;
        $this->sandbox = $sandbox;
        $this->baseUrl = $sandbox
            ? 'https://courier-api-sandbox.pathao.com'
            : 'https://api-hermes.pathao.com';

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Get access token with caching
     *
     * @return string
     * @throws PathaoException
     */
    protected function getAccessToken(): string
    {
        $cacheKey = self::CACHE_PREFIX . 'access_token';
        
        // Try to get cached token
        $cachedToken = Cache::get($cacheKey);
        if ($cachedToken && isset($cachedToken['token']) && isset($cachedToken['expires_at'])) {
            // Check if token is still valid (with 5 minute buffer)
            if ($cachedToken['expires_at'] > now()->addMinutes(5)->timestamp) {
                return $cachedToken['token'];
            }
        }

        // Get new token
        try {
            $response = $this->client->post('/aladdin/api/v1/issue-token', [
                'json' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'username' => $this->username,
                    'password' => $this->password,
                    'grant_type' => 'password',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!isset($data['access_token'])) {
                throw new PathaoException('Failed to get access token: Invalid response from Pathao API');
            }

            $token = $data['access_token'];
            $expiresIn = $data['expires_in'] ?? 432000; // Default 5 days

            // Cache token (subtract 5 minutes for safety)
            Cache::put($cacheKey, [
                'token' => $token,
                'expires_at' => now()->addSeconds($expiresIn - 300)->timestamp,
            ], now()->addSeconds($expiresIn - 300));

            return $token;

        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);
            $message = $response['message'] ?? 'Failed to authenticate with Pathao API';
            throw new PathaoException($message, $e->getCode());
        } catch (GuzzleException $e) {
            throw new PathaoException('Network error: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get access token from refresh token
     *
     * @param string $refreshToken
     * @return array Returns array with access_token, refresh_token, expires_in
     * @throws PathaoException
     */
    public function refreshAccessToken(string $refreshToken): array
    {
        try {
            $response = $this->client->post('/aladdin/api/v1/issue-token', [
                'json' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'refresh_token' => $refreshToken,
                    'grant_type' => 'refresh_token',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!isset($data['access_token'])) {
                throw new PathaoException('Failed to refresh access token: Invalid response from Pathao API');
            }

            $token = $data['access_token'];
            $newRefreshToken = $data['refresh_token'] ?? $refreshToken;
            $expiresIn = $data['expires_in'] ?? 432000; // Default 5 days

            // Cache the new token
            $cacheKey = self::CACHE_PREFIX . 'access_token';
            Cache::put($cacheKey, [
                'token' => $token,
                'expires_at' => now()->addSeconds($expiresIn - 300)->timestamp,
            ], now()->addSeconds($expiresIn - 300));

            return [
                'access_token' => $token,
                'refresh_token' => $newRefreshToken,
                'expires_in' => $expiresIn,
            ];

        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);
            $message = $response['message'] ?? 'Failed to refresh access token';
            throw new PathaoException($message, $e->getCode());
        } catch (GuzzleException $e) {
            throw new PathaoException('Network error: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Check and enforce rate limiting
     *
     * @param string $endpoint
     * @return void
     * @throws PathaoException
     */
    protected function checkRateLimit(string $endpoint): void
    {
        $cacheKey = self::CACHE_PREFIX . 'rate_limit_' . md5($endpoint);
        $currentMinute = now()->format('Y-m-d-H-i');
        $minuteKey = $cacheKey . '_' . $currentMinute;

        $requestCount = Cache::get($minuteKey, 0);

        if ($requestCount >= self::RATE_LIMIT_PER_MINUTE) {
            throw new PathaoException('Rate limit exceeded. Maximum ' . self::RATE_LIMIT_PER_MINUTE . ' requests per minute.');
        }

        // Increment counter (expires in 2 minutes to be safe)
        Cache::put($minuteKey, $requestCount + 1, now()->addMinutes(2));
    }

    /**
     * Make API request
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @param bool $requiresAuth
     * @return array
     * @throws PathaoException
     */
    protected function request(string $method, string $endpoint, array $data = [], bool $requiresAuth = true): array
    {
        // Check rate limit
        $this->checkRateLimit($endpoint);

        $options = [];

        if (!empty($data)) {
            $options['json'] = $data;
        }

        if ($requiresAuth) {
            $token = $this->getAccessToken();
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token,
            ];
        }

        try {
            // Log the actual request URL for debugging
            $fullUrl = $this->baseUrl . $endpoint;
           
            
            $response = $this->client->request($method, $endpoint, $options);
            $responseBody = json_decode($response->getBody()->getContents(), true);
            
            
            return $responseBody;
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $body = json_decode($response->getBody()->getContents(), true);
            
            $message = $body['message'] ?? 'API request failed';
            $code = $response->getStatusCode();
            
            Log::error('Pathao API Error', [
                'endpoint' => $endpoint,
                'method' => $method,
                'status' => $code,
                'message' => $message,
                'response' => $body,
            ]);

            throw new PathaoException($message, $code, $body['errors'] ?? []);
        } catch (GuzzleException $e) {
            Log::error('Pathao Network Error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            throw new PathaoException('Network error: ' . $e->getMessage(), $e->getCode());
        }
    }
}

