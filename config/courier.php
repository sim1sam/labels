<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Courier API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for courier API integrations
    |
    */

    'steadfast' => [
        'enabled' => env('STEADFAST_API_ENABLED', true),
        'mock_in_local' => env('STEADFAST_MOCK_IN_LOCAL', true),
        'base_url' => env('STEADFAST_BASE_URL', 'https://portal.steadfast.com.bd/api/v1'),
        'timeout' => env('STEADFAST_TIMEOUT', 30),
    ],

    'default' => [
        'enabled' => env('COURIER_API_ENABLED', true),
        'mock_in_local' => env('COURIER_MOCK_IN_LOCAL', true),
    ],
];

