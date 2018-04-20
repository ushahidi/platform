<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/**
 * API version number
 */
$apiVersion = '3';
$apiBase = 'api/v' . $apiVersion;

$router->get('/', "API\IndexController@index");
$router->get($apiBase, "API\IndexController@index");
$router->group([
    'prefix' => $apiBase,
    'namespace' => 'API'
], function () use ($router) {

    // API keys
    $router->group([
        'middleware' => ['auth:api', 'scope:apikeys'],
        'prefix' => 'apikeys'
    ], function () use ($router) {
        $router->get('/', 'ApiKeysController@index');
        $router->post('/', 'ApiKeysController@store');
        $router->get('/{id}', 'ApiKeysController@show');
        $router->put('/{id}', 'ApiKeysController@update');
        $router->delete('/{id}', 'ApiKeysController@destroy');
    });

    // Collections
    $router->group([
            'namespace' => 'Collections',
            'prefix' => 'collections',
            'middleware' => ['scope:collections,sets']
    ], function () use ($router) {
        // Public access
        $router->get('/', 'CollectionsController@index');
        $router->get('/{id}', 'CollectionsController@show');
        $router->group(['prefix' => '/{set_id:[0-9]+}/posts'], function () use ($router) {
            $router->get('/', 'PostsController@index');
            $router->get('/{id}', 'PostsController@show');
        });

        // Restricted access
        $router->group([
            'middleware' => ['auth:api', 'scope:collections,sets']
        ], function () use ($router) {
            $router->post('/', 'CollectionsController@store');
            $router->put('/{id}', 'CollectionsController@update');
            $router->delete('/{id}', 'CollectionsController@destroy');

            $router->group(['prefix' => '/{set_id:[0-9]+}/posts'], function () use ($router) {
                $router->post('/', 'PostsController@store');
                //$router->put('/{id}', 'PostsController@update');
                $router->delete('/{id}', 'PostsController@destroy');
            });
        });
    });

    // Config
    $router->group([
        'prefix' => 'config/',
        'middleware' => ['scope:config']
    ], function () use ($router) {
        // Public access
        $router->get('/', ['uses' => 'ConfigController@index']);
        // @todo stop using this in client, and remove?
        $router->options('/', ['uses' => 'ConfigController@indexOptions']);
        $router->get('/{id}', ['uses' => 'ConfigController@show']);

        // Restricted access
        $router->group(['middleware' => ['auth:api', 'scope:config']], function () use ($router) {
            // $router->post('/', ['middleware' => 'oauth:config', 'uses' => 'ConfigController@store']);
            $router->put('/{id}', ['uses' => 'ConfigController@update']);
            // $router->delete('/{id}', ['middleware' => 'oauth:config', 'uses' => 'ConfigController@destroy']);
        });
    });

    // Contacts
    $router->group([
        'middleware' => ['auth:api', 'scope:contacts'],
        'prefix' => 'contacts'
    ], function () use ($router) {
        $router->get('/', 'ContactsController@index');
        $router->post('/', 'ContactsController@store');
        $router->get('/{id}', 'ContactsController@show');
        $router->put('/{id}', 'ContactsController@update');
        $router->delete('/{id}', 'ContactsController@destroy');
    });

    // CSV + Import
    $router->group([
        'middleware' => ['auth:api', 'scope:csv'],
        'namespace' => 'CSV',
        'prefix' => 'csv'
    ], function () use ($router) {
        $router->get('/', 'CSVController@index');
        $router->post('/', 'CSVController@store');
        $router->get('/{id}', 'CSVController@show');
        $router->put('/{id}', 'CSVController@update');
        $router->delete('/{id}', 'CSVController@destroy');

        $router->post('/{id}/import', 'CSVImportController@store');
    });

    // Country Codes
    $router->group([
        'middleware' => ['auth:api', 'scope:country_codes'],
        'prefix' => 'country-codes'
    ], function () use ($router) {
        $router->get('/', 'CountryCodesController@index');
        $router->get('/{id}', 'CountryCodesController@show');
    });

    // Data providers
    $router->group([
        'middleware' => ['auth:api', 'scope:dataproviders'],
        'prefix' => 'dataproviders'
    ], function () use ($router) {
        $router->get('/', 'DataProvidersController@index');
        $router->get('/{id}', 'DataProvidersController@show');
    });

    // Export Jobs
    $router->group([
        'namespace' => 'Exports',
        'middleware' => ['auth:api', 'scope:posts'],
        'prefix' => '/exports/jobs'
    ], function () use ($router) {
        $router->get('/', 'JobsController@index');
        $router->post('/', 'JobsController@store');
        $router->get('/{id}', 'JobsController@show');
        $router->put('/{id}', 'JobsController@update');
        $router->delete('/{id}', 'JobsController@destroy');


        $router->group([
            'namespace' => 'External',
            'middleware' => ['signature'],
            'prefix' => '/external'
        ], function () use ($router) {
            // External jobs
            $router->get('/jobs', 'JobsController@index');
            $router->get('/jobs/{id:[0-9]+}', 'JobsController@show');
            $router->put('/jobs/{id:[0-9]+}', 'JobsController@update');

            // Count export
            $router->get('/count/{id:[0-9]+}', 'CountController@show');

            // Run CLI for export
            // @todo this should not be a get
            $router->get('/cli/{id:[0-9]+}', 'CliController@show');
        });
    });

    // Forms
    $router->group([
        'namespace' => 'Forms',
        'prefix' => 'forms',
        'middleware' => ['scope:forms']
    ], function () use ($router) {
        // Public access
        $router->get('/', 'FormsController@index');
        $router->get('/{id:[0-9]+}', 'FormsController@show');

        $router->get('/attributes', 'AttributesController@index');
        $router->get('/stages', 'StagesController@index');

        // Sub-form routes
        $router->group(['prefix' => '/{form_id:[0-9]+}'], function () use ($router) {
            // Attributes
            $router->group(['prefix' => 'attributes'], function () use ($router) {
                $router->get('/', 'AttributesController@index');
                $router->get('/{id}', 'AttributesController@show');
            });

            // Contacts
            $router->group(['prefix' => 'contacts'], function () use ($router) {
                $router->get('/', 'ContactsController@index');
                $router->get('/{id}', 'ContactsController@show');
                $router->post('/', 'ContactsController@store');
                $router->put('/{id}', 'ContactsController@update');
                $router->delete('/{id}', 'ContactsController@destroy');
            });

            // Stages
            $router->group(['prefix' => 'stages'], function () use ($router) {
                $router->get('/', 'StagesController@index');
                $router->get('/{id}', 'StagesController@show');
            });

            // Stats
            $router->group(['prefix' => 'stats'], function () use ($router) {
                $router->get('/', 'StatsController@index');
            });

            // Roles
            $router->group(['prefix' => 'roles'], function () use ($router) {
                $router->get('/', 'RolesController@index');
            });
        });

        // Restricted access
        $router->group([
            'middleware' => ['auth:api', 'scope:forms']
        ], function () use ($router) {
            $router->post('/', 'FormsController@store');
            $router->put('/{id:[0-9]+}', 'FormsController@update');
            $router->delete('/{id:[0-9]+}', 'FormsController@destroy');

            // Sub-form routes
            $router->group(['prefix' => '/{form_id:[0-9]+}'], function () use ($router) {
                // Attributes
                $router->group(['prefix' => 'attributes'], function () use ($router) {
                    $router->post('/', 'AttributesController@store');
                    $router->put('/{id}', 'AttributesController@update');
                    $router->delete('/{id}', 'AttributesController@destroy');
                });

                // Stages
                $router->group(['prefix' => 'stages'], function () use ($router) {
                    $router->post('/', 'StagesController@store');
                    $router->put('/{id}', 'StagesController@update');
                    $router->delete('/{id}', 'StagesController@destroy');
                });

                // Roles
                $router->group(['prefix' => 'roles'], function () use ($router) {
                    $router->put('/', 'RolesController@replace');
                });
            });
        });
    });

    // Layers
    $router->group([
        'prefix' => 'layers',
        'middleware' => ['scope:layers']
    ], function () use ($router) {
        // Public access
        $router->get('/', 'LayersController@index');
        $router->get('/{id}', 'LayersController@show');

        // Restricted access
        $router->group([
            'middleware' => ['auth:api', 'scope:layers']
        ], function () use ($router) {
            $router->post('/', 'LayersController@store');
            $router->put('/{id}', 'LayersController@update');
            $router->delete('/{id}', 'LayersController@destroy');
        });
    });

    // Media
    $router->group([
        'prefix' => 'media',
        'middleware' => ['scope:media']
    ], function () use ($router) {
        // Public access
        $router->get('/', 'MediaController@index');
        $router->get('/{id}', 'MediaController@show');
        // Public can upload media
        $router->post('/', 'MediaController@store');

        // Restricted access
        $router->group([
            'middleware' => ['auth:api', 'scope:media']
        ], function () use ($router) {
            $router->put('/{id}', 'MediaController@update');
            $router->delete('/{id}', 'MediaController@destroy');
        });
    });

    // Messages
    $router->group([
        'middleware' => ['auth:api', 'scope:messages'],
        'prefix' => 'messages'
    ], function () use ($router) {
        $router->get('/', 'MessagesController@index');
        $router->post('/', 'MessagesController@store');
        $router->get('/{id}', 'MessagesController@show');
        $router->get('/{id}/post', 'MessagesController@showPost');
        $router->put('/{id}', 'MessagesController@update');
        // $router->delete('/{id}', 'MessagesController@destroy');
    });

    // Migration
    $router->group([
        'middleware' => ['auth:api', 'scope:migrate'],
        'prefix' => 'migration'
    ], function () use ($router) {
        $router->get('/', 'MigrationController@index');
        $router->get('/status', 'MigrationController@status');
        $router->post('/rollback', 'MigrationController@rollback');
        $router->post('/migrate', 'MigrationController@migrate');
    });

    // Notifications
    $router->group([
        'middleware' => ['auth:api', 'scope:notifications'],
        'prefix' => 'notifications'
    ], function () use ($router) {
        $router->get('/', 'NotificationsController@index');
        $router->post('/', 'NotificationsController@store');
        $router->get('/{id}', 'NotificationsController@show');
        $router->put('/{id}', 'NotificationsController@update');
        $router->delete('/{id}', 'NotificationsController@destroy');
    });

    // Password reset
    $router->post('/passwordreset', 'PasswordResetController@store');
    $router->post('/passwordreset/confirm', 'PasswordResetController@confirm');

    // Permissions
    $router->group([
        'middleware' => ['auth:api', 'scope:permissions'],
        'prefix' => 'permissions'
    ], function () use ($router) {
        $router->get('/', 'PermissionsController@index');
        $router->get('/{id}', 'PermissionsController@show');
    });

    // Posts
    $router->group([
        'namespace' => 'Posts',
        'prefix' => 'posts',
        'middleware' => ['scope:posts']
    ], function () use ($router) {
        // Public access
        $router->get('/', 'PostsController@index');
        // @todo stop using this in client, and remove?
        $router->options('/', ['uses' => 'PostsController@indexOptions']);
        $router->get('/{id:[0-9]+}', 'PostsController@show');

        // GeoJSON
        $router->get('/geojson', 'GeoJSONController@index');
        $router->get('/geojson/{zoom}/{x}/{y}', 'GeoJSONController@index');
        $router->get('/{id:[0-9]+}/geojson', 'GeoJSONController@show');

        // Export
        $router->get('/export', 'ExportController@index');

        // Stats
        $router->get('/stats', 'PostsController@stats');

        // Sub-post routes
        $router->group(['prefix' => '/{parent_id:[0-9]+}'], function () use ($router) {
            // Revisions
            $router->group(['prefix' => 'revisions'], function () use ($router) {
                $router->get('/', 'RevisionsController@index');
                $router->get('/{id}', 'RevisionsController@show');
            });

            // Translations
            $router->group(['prefix' => 'translations'], function () use ($router) {
                $router->get('/', 'TranslationsController@index');
                $router->get('/{id:[0-9]+}', 'TranslationsController@show');
                $router->get('/{locale:[A-Za-z_]+}', 'TranslationsController@show');
            });
        });

        // Restricted access
        $router->group([
            'middleware' => ['auth:api', 'scope:posts']
        ], function () use ($router) {
            $router->post('/', 'PostsController@store');
            $router->put('/{id:[0-9]+}', 'PostsController@update');
            $router->delete('/{id:[0-9]+}', 'PostsController@destroy');

            // Locks
            $router->put('/{post_id:[0-9]+}/lock', 'LockController@store');
            $router->delete('/{post_id:[0-9]+}/lock', 'LockController@destroy');

            // Sub-post routes
            $router->group(['prefix' => '/{parent_id:[0-9]+}'], function () use ($router) {
                // Translations
                $router->group(['prefix' => 'translations'], function () use ($router) {
                    $router->post('/', 'TranslationsController@store');
                    $router->put('/{id:[0-9]+}', 'TranslationsController@update');
                    $router->delete('/{id:[0-9]+}', 'TranslationsController@destroy');
                    $router->put('/{locale:[A-Za-z_]+}', 'TranslationsController@update');
                    $router->delete('/{locale:[A-Za-z_]+}', 'TranslationsController@destroy');
                });
            });
        });
    });

    // Roles
    $router->group([
        'prefix' => 'roles',
        'middleware' => ['scope:roles']
    ], function () use ($router) {
        // Public access
        $router->get('/', 'RolesController@index');
        $router->get('/{id}', 'RolesController@show');

        // Restricted access
        $router->group([
            'middleware' => ['auth:api', 'scope:roles']
        ], function () use ($router) {
            $router->post('/', 'RolesController@store');
            $router->put('/{id}', 'RolesController@update');
            $router->delete('/{id}', 'RolesController@destroy');
        });
    });

    // Register
    $router->post('/register', 'RegisterController@store');

    // Saved Searches
    $router->group([
        'prefix' => 'savedsearches',
        'middleware' => ['scope:savedsearches']
    ], function () use ($router) {
        // Public access
        $router->get('/', 'SavedSearchesController@index');
        $router->get('/{id}', 'SavedSearchesController@show');

        // Restricted access
        $router->group([
            'middleware' => ['auth:api', 'scope:savedsearches']
        ], function () use ($router) {
            $router->post('/', 'SavedSearchesController@store');
            $router->put('/{id}', 'SavedSearchesController@update');
            $router->delete('/{id}', 'SavedSearchesController@destroy');
        });
    });

    // Tags
    $router->group([
        'prefix' => 'tags',
        'middleware' => ['scope:tags']
    ], function () use ($router) {
        // Public access
        $router->get('/', 'TagsController@index');
        $router->get('/{id}', 'TagsController@show');

        // Restricted access
        $router->group([
            'middleware' => ['auth:api', 'scope:tags']
        ], function () use ($router) {
            $router->post('/', 'TagsController@store');
            $router->put('/{id}', 'TagsController@update');
            $router->delete('/{id}', 'TagsController@destroy');
        });
    });

    // TOS
    $router->group([
        'middleware' => ['auth:api', 'scope:tos'],
        'prefix' => 'tos'
    ], function () use ($router) {
        $router->get('/', 'TosController@index');
        $router->post('/', 'TosController@store');
        $router->get('/{id}', 'TosController@show');
        //$router->put('/{id}', 'TosController@update');
        //$router->delete('/{id}', 'TosController@destroy');
    });

    // Users
    $router->group([
        'prefix' => 'users',
        'middleware' => ['scope:users']
    ], function () use ($router) {
        // Public access
        $router->get('/', 'UsersController@index');
        $router->get('/{id:[0-9]+}', 'UsersController@show');

        // Restricted access
        $router->group([
            'middleware' => ['auth:api', 'scope:users']
        ], function () use ($router) {
            $router->post('/', 'UsersController@store');
            $router->put('/{id:[0-9]+}', 'UsersController@update');
            $router->delete('/{id:[0-9]+}', 'UsersController@destroy');
            $router->get('/me', 'UsersController@showMe');
            $router->put('/me', 'UsersController@updateMe');
        });
    });

    // Web hooks
    $router->group([
        'middleware' => ['auth:api', 'scope:webhooks'],
        'prefix' => 'webhooks'
    ], function () use ($router) {
        $router->get('/', 'WebhooksController@index');
        $router->post('/', 'WebhooksController@store');
        $router->get('/{id:[0-9]+}', 'WebhooksController@show');
        $router->put('/{id:[0-9]+}', 'WebhooksController@update');
        $router->delete('/{id:[0-9]+}', 'WebhooksController@destroy');

        $router->put('/posts', 'WebhookPostsController@update');
    });
});

// Migration
$router->get('/migrate', 'MigrateController@migrate');
