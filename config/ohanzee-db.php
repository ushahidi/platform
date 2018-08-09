<?php

/**
 * Database Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Config
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

// Parse ClearDB URLs
if (getenv("CLEARDB_DATABASE_URL")) {
    $url = parse_url(getenv("CLEARDB_DATABASE_URL"));
    // Push url parts into env
    putenv("DB_HOST=" . $url["host"]);
    putenv("DB_USERNAME=" . $url["user"]);
    putenv("DB_PASSWORD=" . $url["pass"]);
    putenv("DB_DATABASE=" . substr($url["path"], 1));
}

// DB config
$config = [
    'type'       => 'MySQLi',
    'connection' => [
        'hostname'   => getenv('DB_HOST'),
        'database'   => getenv('DB_DATABASE'),
        'username'   => getenv('DB_USERNAME'),
        'password'   => getenv('DB_PASSWORD'),
        'persistent' => false,
    ],
    'table_prefix' => '',
    'charset'      => 'utf8',
    'caching'      => true,
    'profiling'    => true,
];

// If multisite is enabled
if (!empty(getenv("MULTISITE_DOMAIN"))) {
    // Use this config for the multisite db
    return [
        // Just define basics for default connection
        'default'   => [
            'type'         => 'MySQLi',
            'connection'   => [ 'persistent' => false, ],
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => true,
            'profiling'    => true,
        ],
        'multisite' => $config
    ];
} else {
    // Otherwise this is the platform DB config
    return [
        'default' => $config,
        'multisite' => $config
    ];
}
