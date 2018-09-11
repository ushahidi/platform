<?php

/**
 * Ratelimiter Config
 */

return [
    /*  memcached, filesystem or FALSE
     *
     * When set to FALSE, in-memory cache will be used.
     * Please note that this only lasts the lifetime of the request.
     *
     */
    'cache' => getenv('RATELIMITER_CACHE') ?: 'filesystem',
    'filesystem' => [
        'directory' => '/tmp/ratelimitercache',
    ],
    'memcached' => [
        'host' => '127.0.0.1',
        'port' => 11211
    ]
];
