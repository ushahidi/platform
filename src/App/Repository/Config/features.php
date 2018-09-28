<?php

/**
 * Features Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Config
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

return [
    // Determines which features are available in a deployment

    // Post views
    'views' => [
        'data' => true,
        'map' => true,
        'list' => true,
        'chart' => true,
        'timeline' => true,
        'activity' => true,
        'plan' => false,
    ],

    // Data sources
    'data-providers' => [
        'smssync' => true,
        'twitter' => true,
        'frontlinesms' => true,
        'email' => true,
        'outgoingemail' => true,
        'twilio' => true,
        'nexmo' => true,
        'testservice' => false
    ],

    // Client limits
    // Where TRUE is infinite and an integer defines a limit
    'limits' => [
        'posts' => true,
        'forms' => true,
        'admin_users' => true,
    ],

    // Private deployments
    'private' => [
        'enabled' => true,
    ],

    // Roles
    'roles' => [
        'enabled' => true,
    ],

    // Webhooks
    'webhooks' => [
        'enabled' => true,
    ],

    // Data import
    'data-import' => [
        'enabled' => true,
    ],

    // Post locking
    'post-locking' => [
        'enabled' => true,
    ],

    // Targeted Surveys
    'targeted-surveys' => [
        'enabled' => false,
    ],

    // Post locking
    // Defaults to False, tests will fail if set to TRUE
    // without complete Redis setup
    // see TODO <docs link>
    'redis' => [
        'enabled' => false,
    ],

    'csv-speedup' => [
        'enabled' => false,
    ],

    // Enable or disable HXL export to HDX
    // We will need a new 'hxl-download' flag when we do the HXL downloads for P1
    'hxl' => [
        'enabled' => false,
    ],

    // Enable or disable User Settings feature
    'user-settings' => [
        'enabled' => false,
    ],

    // Enable or disable the Anonymisation of Reporters
    // Controls whether users can set obfuscation of location, redaction of date/time
    // and reporter info
    'anonymise-reporters' => [
        'enabled' => true,
    ],
];
