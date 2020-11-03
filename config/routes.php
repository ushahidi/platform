<?php

return [

    // Configure cacheability of responses by browsers and intermediate caches
    'cache_control' => [
        /*
        | Preset level of cacheability.
        |
        | This is a coarse-grained setting to indicate which of all the different
        | api endpoints should have cacheable responses.
        |
        | off      - all content is marked as not cacheable
        | minimal  - allow caching of only the most compute/data-intensive and least
        |            consistency-critical content. This is usually the global
        |            geojson endpoint (which may have a lot of points). On top of that,
        |            caching is only enabled for guest users, not logged-in members.
        */
        'level' => env('CACHE_CONTROL_LEVEL', 'off'),

        /*
        | Longest max-age
        |
        | This value is applied to what the selected cache control level considers
        | the most cacheable responses. Less cacheable responses are assigned a
        | proportionally reduced value.
        |
        | Note that his has no effect if the cache level is set to 'off'
        */
        'max_age' => env('CACHE_CONTROL_MAX_AGE', 600),

        /*
        | Only private caching allowed
        |
        | Set this to true if you don't want responses to be cached in intermediate
        | proxies, but only in the end user's browsers instead.
        */
        'private_only' => env('CACHE_CONTROL_PRIVATE', false),

    ]
];
