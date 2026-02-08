<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Secret Key
    |--------------------------------------------------------------------------
    |
    | The secret key used to sign and verify JWT tokens.
    | Must be at least 32 characters long for HS256 algorithm.
    | Generate with: php artisan key:generate --show (then copy to .env)
    |
    */
    'secret' => env('JWT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | JWT Algorithm
    |--------------------------------------------------------------------------
    |
    | The algorithm used to sign JWT tokens.
    | Supported: HS256, HS384, HS512, RS256, RS384, RS512
    |
    */
    'algorithm' => env('JWT_ALGORITHM', 'HS256'),

    /*
    |--------------------------------------------------------------------------
    | Access Token TTL (in minutes)
    |--------------------------------------------------------------------------
    |
    | The time-to-live for access tokens in minutes.
    | Default: 15 minutes
    |
    */
    'access_token_ttl' => env('JWT_ACCESS_TOKEN_TTL', 15),

    /*
    |--------------------------------------------------------------------------
    | Refresh Token TTL (in minutes)
    |--------------------------------------------------------------------------
    |
    | The time-to-live for refresh tokens in minutes.
    | Default: 10080 minutes (7 days)
    |
    */
    'refresh_token_ttl' => env('JWT_REFRESH_TOKEN_TTL', 10080),
];