<?php

return [
    'paths' => [
        'migrations' => __DIR__ . '/database/migrations/phinx',
        'seeds' => __DIR__ . '/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'ushahidi',
        'ushahidi' => [
            'adapter' => 'mysql', // todo: how to make this dynamic?
            'host' => getenv('DB_HOST'),
            'name' => getenv('DB_DATABASE'),
            'user' => getenv('DB_USERNAME'),
            'pass' => getenv('DB_PASSWORD'),
            'unix_socket' => getenv('DB_SOCKET'),
            'charset' => 'utf8',
        ],
    ]
];
