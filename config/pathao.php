<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pathao Courier API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Pathao Courier Merchant API integration.
    | Set sandbox to true for testing, false for production.
    |
    */

    'sandbox' => env('PATHAO_SANDBOX', false),

    'client_id' => env('PATHAO_CLIENT_ID', ''),

    'client_secret' => env('PATHAO_CLIENT_SECRET', ''),

    'username' => env('PATHAO_USERNAME', ''),

    'password' => env('PATHAO_PASSWORD', ''),

    'store_id' => env('PATHAO_STORE_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache settings for access tokens and API responses.
    |
    */

    'cache' => [
        'prefix' => 'pathao_courier_',
        'token_ttl' => 432000, // 5 days in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limiting settings to prevent exceeding API limits.
    |
    */

    'rate_limit' => [
        'enabled' => env('PATHAO_RATE_LIMIT_ENABLED', true),
        'requests_per_minute' => env('PATHAO_RATE_LIMIT_PER_MINUTE', 60),
    ],
];

