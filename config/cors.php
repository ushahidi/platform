<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS Options
    |--------------------------------------------------------------------------
    |
    | The allowed_methods and allowed_headers options are case-insensitive.
    |
    | You don't need to provide both allowed_origins and allowed_origins_patterns.
    | If one of the strings passed matches, it is considered a valid origin.
    |
    | If ['*'] is provided to allowed_methods, allowed_origins or allowed_headers
    | all methods / origins / headers are allowed.
    |
    */

    'supports_credentials' => true,
    'paths' => ['api/*', 'oauth/*'],
    'allowed_origins' => ['*'],
    'allowed_headers' => ['Authorization', 'Content-type', 'Accept'],
    'allowed_methods' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
];
