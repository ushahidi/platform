<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('S3_BUCKET', env('AWS_BUCKET')),
            # for S3 services outside AWS
            'endpoint' => env('S3_ENDPOINT'),
            # subpath within bucket
            'root' => env('S3_ROOT'),
            # neededs to be true for i.e. minio
            'use_path_style_endpoint' => (bool) env('S3_USE_PATH_STYLE_ENDPOINT', false),
            # base URL where uploaded objects can be accessed, useful for CDN
            'url' => env('S3_PUBLIC_URL'),
        ],

        'rackspace' => [
            'driver'    => 'rackspace',
            'username'  => env('RS_USERNAME'),
            'key'       => env('RS_APIKEY'),
            'container' => env('RS_CONTAINER'),
            'authUrl'   => 'https://lon.identity.api.rackspacecloud.com/v2.0/',
            'region'    => env('RS_REGION'),
            'tenantid'  => env('RS_TENANTID', '1'),
        ],

    ],

];
