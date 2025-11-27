<?php

namespace Nayemuf\PathaoCourier;

use Nayemuf\PathaoCourier\Apis\AreaApi;
use Nayemuf\PathaoCourier\Apis\OrderApi;
use Nayemuf\PathaoCourier\Apis\PriceApi;
use Nayemuf\PathaoCourier\Apis\StoreApi;

class PathaoCourier
{
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
     * @var string|null
     */
    protected $storeId;

    /**
     * @var OrderApi
     */
    protected $orderApi;

    /**
     * @var AreaApi
     */
    protected $areaApi;

    /**
     * @var StoreApi
     */
    protected $storeApi;

    /**
     * @var PriceApi
     */
    protected $priceApi;

    /**
     * PathaoCourier constructor.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $username
     * @param string $password
     * @param bool $sandbox
     * @param string|null $storeId
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        string $username,
        string $password,
        bool $sandbox = false,
        ?string $storeId = null
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->username = $username;
        $this->password = $password;
        $this->sandbox = $sandbox;
        $this->storeId = $storeId;
    }

    /**
     * Refresh access token using refresh token
     *
     * @param string $refreshToken
     * @return array
     * @throws \Nayemuf\PathaoCourier\Exceptions\PathaoException
     */
    public function refreshToken(string $refreshToken): array
    {
        // Use OrderApi instance to access BaseApi method
        $orderApi = $this->order();
        return $orderApi->refreshAccessToken($refreshToken);
    }

    /**
     * Get Order API instance
     *
     * @return OrderApi
     */
    public function order(): OrderApi
    {
        if (!$this->orderApi) {
            $this->orderApi = new OrderApi(
                $this->clientId,
                $this->clientSecret,
                $this->username,
                $this->password,
                $this->sandbox
            );
        }

        return $this->orderApi;
    }

    /**
     * Get Area API instance
     *
     * @return AreaApi
     */
    public function area(): AreaApi
    {
        if (!$this->areaApi) {
            $this->areaApi = new AreaApi(
                $this->clientId,
                $this->clientSecret,
                $this->username,
                $this->password,
                $this->sandbox
            );
        }

        return $this->areaApi;
    }

    /**
     * Get Store API instance
     *
     * @return StoreApi
     */
    public function store(): StoreApi
    {
        if (!$this->storeApi) {
            $this->storeApi = new StoreApi(
                $this->clientId,
                $this->clientSecret,
                $this->username,
                $this->password,
                $this->sandbox
            );
        }

        return $this->storeApi;
    }

    /**
     * Get Price API instance
     *
     * @return PriceApi
     */
    public function price(): PriceApi
    {
        if (!$this->priceApi) {
            $this->priceApi = new PriceApi(
                $this->clientId,
                $this->clientSecret,
                $this->username,
                $this->password,
                $this->sandbox,
                $this->storeId
            );
        }

        return $this->priceApi;
    }
}

