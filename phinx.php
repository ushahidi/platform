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
            // afaik, phinx doesn't really use this for table creation in v0.11.7
            // apparently it's used as a connection parameter
            'charset' => 'utf8mb4',
            // phinx guesses the charset to create the table with from this
            // defaults to MariaDB's utf8mb4_unicode_520_ci, which is not supported by MySQL
            // for MySQL you would want to use utf8mb4_0900_ai_ci (MySQL 8.0.17+)
            // if you are stuck with MySQL 5.7, you can use utf8mb4_unicode_ci
            'collation' => getenv('DB_COLLATION') ?: 'utf8mb4_unicode_520_ci'
        ],
    ]
];
