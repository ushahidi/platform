<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ushahidi Platform Setting
    |--------------------------------------------------------------------------
    |
    | This is the main configuration file for the Ushahidi Platform.
    */

    'groups' => [
        'features',
        'site',
        'deployment_id',
        'test',
        'data-provider',
        'map',
        'twitter',
        'gmail'
    ],

    'site' => [
        'name' => '',
        'description' => '',
        'email' => '',
        'timezone' => 'UTC',
        'language' => 'en-US',
        'date_format' => 'n/j/Y',
        'client_url' => false,
        'first_login' => true,
        'tier' => 'free',
        'private' => false,
        'api_version' => 'v5',
        'donation' => [
            'enabled' => false,
            'title' => '',
            'description' => '',
            'wallet' => '',
            'images' => []
        ],
    ],

    'map' => [
        // Enable marker clustering with leaflet.markercluster
        'clustering' => true,
        'cluster_radius' => 50,
        'location_precision' => 2,
        // Map start location
        'default_view' => [
            'lat' => -1.3048035,
            'lon' => 36.8473969,
            'zoom' => 2,
            'baselayer' => 'MapQuest',
            'fit_map_boundaries' => true,
            // Fit map boundaries to current data rendered
            'icon' => 'map-marker',
            // Fontawesome Markers
            'color' => 'blue'
        ]
    ],

    'data-provider' => config('data-provider'),

    'features' => config('features'),
];
