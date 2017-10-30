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
$router->get($apiBase . '[/]', "API\IndexController@index");
$router->group(['prefix' => $apiBase, 'namespace' => 'API'], function () use ($router) {

    // Collections
    $router->group([
            'middleware' => ['auth:api', 'scope:collections,sets'],
            'namespace' => 'Collections'
    ], function () use ($router) {
        $router->get('/collections[/]', 'CollectionsController@index');
        $router->post('/collections[/]', 'CollectionsController@store');
        $router->group(['prefix' => 'collections/'], function () use ($router) {
            $router->get('/{id}[/]', 'CollectionsController@show');
            $router->put('/{id}[/]', 'CollectionsController@update');
            $router->delete('/{id}[/]', 'CollectionsController@destroy');

            $router->get('/{set_id}/posts[/]', 'PostsController@index');
            $router->post('/{set_id}/posts[/]', 'PostsController@store');
            $router->group(['prefix' => '/{set_id:[0-9]+}/posts/'], function () use ($router) {
                $router->get('/{id}[/]', 'PostsController@show');
                //$router->put('/{id}[/]', 'PostsController@update');
                $router->delete('/{id}[/]', 'PostsController@destroy');
            });
        });
    });

    // Config
    // Define /config outside the group otherwise prefix breaks optional trailing slash
    $router->get('/config[/]', ['uses' => 'ConfigController@index']);
    // @todo stop using this in client, and remove?
    $router->options('/config[/]', ['uses' => 'ConfigController@indexOptions']);
    // $router->post('/config[/]', ['middleware' => 'oauth:config', 'uses' => 'ConfigController@store']);
    $router->group(['prefix' => 'config/'], function () use ($router) {
        $router->get('/{id}[/]', ['middleware' => ['auth:api', 'scope:config'], 'uses' => 'ConfigController@show']);
        $router->put('/{id}[/]', ['middleware' => ['auth:api', 'scope:config'], 'uses' => 'ConfigController@update']);
        // $router->delete('/{id}[/]', ['middleware' => 'oauth:config', 'uses' => 'ConfigController@destroy']);
    });

    // Contacts
    $router->group(['middleware' => ['auth:api', 'scope:contacts']], function () use ($router) {
        $router->get('/contacts[/]', 'ContactsController@index');
        $router->post('/contacts[/]', 'ContactsController@store');
        $router->group(['prefix' => 'contacts/'], function () use ($router) {
            $router->get('/{id}[/]', 'ContactsController@show');
            $router->put('/{id}[/]', 'ContactsController@update');
            $router->delete('/{id}[/]', 'ContactsController@destroy');
        });
    });

    // CSV + Import
    $router->group(['middleware' => ['auth:api', 'scope:csv'], 'namespace' => 'CSV'], function () use ($router) {
        $router->get('/csv[/]', 'CSVController@index');
        $router->post('/csv[/]', 'CSVController@store');
        $router->group(['prefix' => 'csv/'], function () use ($router) {
            $router->get('/{id}[/]', 'CSVController@show');
            $router->put('/{id}[/]', 'CSVController@update');
            $router->delete('/{id}[/]', 'CSVController@destroy');

            $router->post('/{id}/import[/]', 'CSVImportController@store');
        });
    });


    // Data providers
    $router->group(['middleware' => ['auth:api', 'scope:dataproviders']], function () use ($router) {
        $router->get('/dataproviders[/]', 'DataProvidersController@index');
        $router->get('/dataproviders/{id}[/]', 'DataProvidersController@show');
    });

    // Forms
    $router->group(['middleware' => ['auth:api', 'scope:forms'], 'namespace' => 'Forms'], function () use ($router) {
        $router->get('/forms[/]', 'FormsController@index');
        $router->post('/forms[/]', 'FormsController@store');
        $router->group(['prefix' => 'forms/'], function () use ($router) {
            $router->get('/{id:[0-9]+}[/]', 'FormsController@show');
            $router->put('/{id:[0-9]+}[/]', 'FormsController@update');
            $router->delete('/{id:[0-9]+}[/]', 'FormsController@destroy');

            $router->get('/attributes[/]', 'AttributesController@index');
            $router->get('/stages[/]', 'StagesController@index');

            // Sub-form routes
            $router->group(['prefix' => '/{form_id:[0-9]+}'], function () use ($router) {
                // Attributes
                $router->get('/attributes[/]', 'AttributesController@index');
                $router->post('/attributes[/]', 'AttributesController@store');
                $router->group(['prefix' => 'attributes/'], function () use ($router) {
                    $router->get('/{id}[/]', 'AttributesController@show');
                    $router->put('/{id}[/]', 'AttributesController@update');
                    $router->delete('/{id}[/]', 'AttributesController@destroy');
                });

                // Stages
                $router->get('/stages[/]', 'StagesController@index');
                $router->post('/stages[/]', 'StagesController@store');
                $router->group(['prefix' => 'stages/'], function () use ($router) {
                    $router->get('/{id}[/]', 'StagesController@show');
                    $router->put('/{id}[/]', 'StagesController@update');
                    $router->delete('/{id}[/]', 'StagesController@destroy');
                });

                // Roles
                $router->get('/roles[/]', 'RolesController@index');
                $router->put('/roles[/]', 'RolesController@replace');
            });
        });
    });

    // Layers
    $router->group(['middleware' => ['auth:api', 'scope:layers']], function () use ($router) {
        $router->get('/layers[/]', 'LayersController@index');
        $router->post('/layers[/]', 'LayersController@store');
        $router->group(['prefix' => 'layers/'], function () use ($router) {
            $router->get('/{id}[/]', 'LayersController@show');
            $router->put('/{id}[/]', 'LayersController@update');
            $router->delete('/{id}[/]', 'LayersController@destroy');
        });
    });

    // Media
    $router->group(['middleware' => ['auth:api', 'scope:media']], function () use ($router) {
        $router->get('/media[/]', 'MediaController@index');
        $router->post('/media[/]', 'MediaController@store');
        $router->group(['prefix' => 'media/'], function () use ($router) {
            $router->get('/{id}[/]', 'MediaController@show');
            $router->put('/{id}[/]', 'MediaController@update');
            $router->delete('/{id}[/]', 'MediaController@destroy');
        });
    });

    // Messages
    $router->group(['middleware' => ['auth:api', 'scope:messages']], function () use ($router) {
        $router->get('/messages[/]', 'MessagesController@index');
        $router->post('/messages[/]', 'MessagesController@store');
        $router->group(['prefix' => 'messages/'], function () use ($router) {
            $router->get('/{id}[/]', 'MessagesController@show');
            $router->get('/{id}/post[/]', 'MessagesController@showPost');
            $router->put('/{id}[/]', 'MessagesController@update');
            // $router->delete('/{id}[/]', 'MessagesController@destroy');
        });
    });

    // Migration
    $router->group(['middleware' => ['auth:api', 'scope:migrate']], function () use ($router) {
        $router->get('/migration[/]', 'MigrationController@index');
        $router->get('/migration/status[/]', 'MigrationController@status');
        $router->post('/migration/rollback[/]', 'MigrationController@rollback');
        $router->post('/migration/migrate[/]', 'MigrationController@migrate');
    });

    // Notifications
    $router->group(['middleware' => ['auth:api', 'scope:notifications']], function () use ($router) {
        $router->get('/notifications[/]', 'NotificationsController@index');
        $router->post('/notifications[/]', 'NotificationsController@store');
        $router->group(['prefix' => 'notifications/'], function () use ($router) {
            $router->get('/{id}[/]', 'NotificationsController@show');
            $router->put('/{id}[/]', 'NotificationsController@update');
            $router->delete('/{id}[/]', 'NotificationsController@destroy');
        });
    });

    // Password reset
    $router->post('/passwordreset[/]', 'PasswordResetController@store');
    $router->post('/passwordreset/confirm[/]', 'PasswordResetController@confirm');

    // Permissions
    $router->group(['middleware' => ['auth:api', 'scope:permissions']], function () use ($router) {
        $router->get('/permissions[/]', 'PermissionsController@index');
        $router->get('/permissions/{id}[/]', 'PermissionsController@show');
    });

    // Posts
    $router->group(['middleware' => ['auth:api', 'scope:posts'], 'namespace' => 'Posts'], function () use ($router) {
        $router->get('/posts[/]', 'PostsController@index');
        // @todo stop using this in client, and remove?
        $router->options('/posts[/]', ['uses' => 'PostsController@indexOptions']);
        $router->post('/posts[/]', 'PostsController@store');
        $router->group(['prefix' => 'posts/'], function () use ($router) {
            $router->get('/{id:[0-9]+}[/]', 'PostsController@show');
            $router->put('/{id:[0-9]+}[/]', 'PostsController@update');
            $router->delete('/{id:[0-9]+}[/]', 'PostsController@destroy');
            // GeoJSON
            $router->get('/geojson[/]', 'GeoJSONController@index');
            $router->get('/geojson/{zoom}/{x}/{y}[/]', 'GeoJSONController@index');
            $router->get('/{id:[0-9]+}/geojson[/]', 'GeoJSONController@show');

            // Locks
            $router->put('/{post_id:[0-9]+}/lock[/]', 'LockController@store');
            $router->delete('/{post_id:[0-9]+}/lock[/]', 'LockController@destroy');

            // Export
            $router->get('/export[/]', 'ExportController@index');

            // Stats
            $router->get('/stats[/]', 'PostsController@stats');

            // Sub-form routes
            $router->group(['prefix' => '/{parent_id:[0-9]+}'], function () use ($router) {
                // Revisions
                $router->get('/revisions[/]', 'RevisionsController@index');
                $router->group(['prefix' => 'revisions/'], function () use ($router) {
                    $router->get('/{id}[/]', 'RevisionsController@show');
                });

                // Translations
                $router->get('/translations[/]', 'TranslationsController@index');
                $router->post('/translations[/]', 'TranslationsController@store');
                $router->group(['prefix' => 'translations/'], function () use ($router) {
                    $router->get('/{id:[0-9]+}[/]', 'TranslationsController@show');
                    $router->put('/{id:[0-9]+}[/]', 'TranslationsController@update');
                    $router->delete('/{id:[0-9]+}[/]', 'TranslationsController@destroy');
                    $router->get('/{locale:[A-Za-z_]+}[/]', 'TranslationsController@show');
                    $router->put('/{locale:[A-Za-z_]+}[/]', 'TranslationsController@update');
                    $router->delete('/{locale:[A-Za-z_]+}[/]', 'TranslationsController@destroy');
                });
            });
        });
    });

    // Roles
    $router->group(['middleware' => ['auth:api', 'scope:roles']], function () use ($router) {
        $router->get('/roles[/]', 'RolesController@index');
        $router->post('/roles[/]', 'RolesController@store');
        $router->group(['prefix' => 'roles/'], function () use ($router) {
            $router->get('/{id}[/]', 'RolesController@show');
            $router->put('/{id}[/]', 'RolesController@update');
            $router->delete('/{id}[/]', 'RolesController@destroy');
        });
    });

    // Register
    $router->post('/register[/]', 'RegisterController@store');

    // Saved Searches
    $router->group(['middleware' => ['auth:api', 'scope:savedsearches']], function () use ($router) {
        $router->get('/savedsearches[/]', 'SavedSearchesController@index');
        $router->post('/savedsearches[/]', 'SavedSearchesController@store');
        $router->group(['prefix' => 'savedsearches/'], function () use ($router) {
            $router->get('/{id}[/]', 'SavedSearchesController@show');
            $router->put('/{id}[/]', 'SavedSearchesController@update');
            $router->delete('/{id}[/]', 'SavedSearchesController@destroy');
        });
    });

    // Tags
    $router->group(['middleware' => ['auth:api', 'scope:tags']], function () use ($router) {
        $router->get('/tags[/]', 'TagsController@index');
        $router->post('/tags[/]', 'TagsController@store');
        $router->group(['prefix' => 'tags/'], function () use ($router) {
            $router->get('/{id}[/]', 'TagsController@show');
            $router->put('/{id}[/]', 'TagsController@update');
            $router->delete('/{id}[/]', 'TagsController@destroy');
        });
    });

    // TOS
    $router->group(['middleware' => ['auth:api', 'scope:tos']], function () use ($router) {
        $router->get('/tos[/]', 'TosController@index');
        $router->post('/tos[/]', 'TosController@store');
        $router->group(['prefix' => 'tos/'], function () use ($router) {
            $router->get('/{id}[/]', 'TosController@show');
            //$router->put('/{id}[/]', 'TosController@update');
            //$router->delete('/{id}[/]', 'TosController@destroy');
        });
    });

    // Users
    $router->group(['middleware' => ['auth:api', 'scope:users']], function () use ($router) {
        $router->get('/users[/]', 'UsersController@index');
        $router->post('/users[/]', 'UsersController@store');
        $router->group(['prefix' => 'users/'], function () use ($router) {
            $router->get('/{id:[0-9]+}[/]', 'UsersController@show');
            $router->put('/{id:[0-9]+}[/]', 'UsersController@update');
            $router->delete('/{id:[0-9]+}[/]', 'UsersController@destroy');
            $router->get('/me[/]', 'UsersController@showMe');
            $router->put('/me[/]', 'UsersController@updateMe');
        });
    });

    // Web hooks
    $router->group(['middleware' => ['auth:api', 'scope:webhooks']], function () use ($router) {
        $router->get('/webhooks[/]', 'WebhooksController@index');
        $router->post('/webhooks[/]', 'WebhooksController@store');
        $router->group(['prefix' => 'webhooks/'], function () use ($router) {
            $router->get('/{id:[0-9]+}[/]', 'WebhooksController@show');
            $router->put('/{id:[0-9]+}[/]', 'WebhooksController@update');
            $router->delete('/{id:[0-9]+}[/]', 'WebhooksController@destroy');
        });
    });
});

// Migration
$router->get('/migrate[/]', 'MigrateController@migrate');
