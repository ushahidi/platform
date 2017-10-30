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
$router->group(['prefix' => $apiBase, 'namespace' => 'API'], function () use ($router) {

    // Collections
    $router->group([
            'middleware' => ['auth:api', 'scope:collections,sets'],
            'namespace' => 'Collections',
            'prefix' => 'collections'
    ], function () use ($router) {
        $router->get('/', 'CollectionsController@index');
        $router->post('/', 'CollectionsController@store');
        $router->get('/{id}', 'CollectionsController@show');
        $router->put('/{id}', 'CollectionsController@update');
        $router->delete('/{id}', 'CollectionsController@destroy');

        $router->group(['prefix' => '/{set_id:[0-9]+}/posts'], function () use ($router) {
            $router->get('/', 'PostsController@index');
            $router->post('/', 'PostsController@store');
            $router->get('/{id}', 'PostsController@show');
            //$router->put('/{id}', 'PostsController@update');
            $router->delete('/{id}', 'PostsController@destroy');
        });
    });

    // Config
    // Define /config outside the group otherwise prefix breaks optional trailing slash
    $router->get('/config', ['uses' => 'ConfigController@index']);
    // @todo stop using this in client, and remove?
    $router->options('/config', ['uses' => 'ConfigController@indexOptions']);
    // $router->post('/config', ['middleware' => 'oauth:config', 'uses' => 'ConfigController@store']);
    $router->group(['prefix' => 'config/'], function () use ($router) {
        $router->get('/{id}', ['middleware' => ['auth:api', 'scope:config'], 'uses' => 'ConfigController@show']);
        $router->put('/{id}', ['middleware' => ['auth:api', 'scope:config'], 'uses' => 'ConfigController@update']);
        // $router->delete('/{id}', ['middleware' => 'oauth:config', 'uses' => 'ConfigController@destroy']);
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


    // Data providers
    $router->group([
        'middleware' => ['auth:api', 'scope:dataproviders'],
        'prefix' => 'dataproviders'
    ], function () use ($router) {
        $router->get('/', 'DataProvidersController@index');
        $router->get('/{id}', 'DataProvidersController@show');
    });

    // Forms
    $router->group([
        'middleware' => ['auth:api', 'scope:forms'],
        'namespace' => 'Forms',
        'prefix' => 'forms'
    ], function () use ($router) {
        $router->get('/', 'FormsController@index');
        $router->post('/', 'FormsController@store');
        $router->get('/{id:[0-9]+}', 'FormsController@show');
        $router->put('/{id:[0-9]+}', 'FormsController@update');
        $router->delete('/{id:[0-9]+}', 'FormsController@destroy');

        $router->get('/attributes', 'AttributesController@index');
        $router->get('/stages', 'StagesController@index');

        // Sub-form routes
        $router->group(['prefix' => '/{form_id:[0-9]+}'], function () use ($router) {
            // Attributes
            $router->group(['prefix' => 'attributes'], function () use ($router) {
                $router->get('/', 'AttributesController@index');
                $router->post('/', 'AttributesController@store');
                $router->get('/{id}', 'AttributesController@show');
                $router->put('/{id}', 'AttributesController@update');
                $router->delete('/{id}', 'AttributesController@destroy');
            });

            // Stages
            $router->group(['prefix' => 'stages'], function () use ($router) {
                $router->get('/', 'StagesController@index');
                $router->post('/', 'StagesController@store');
                $router->get('/{id}', 'StagesController@show');
                $router->put('/{id}', 'StagesController@update');
                $router->delete('/{id}', 'StagesController@destroy');
            });

            // Roles
            $router->group(['prefix' => 'roles'], function () use ($router) {
                $router->get('/', 'RolesController@index');
                $router->put('/', 'RolesController@replace');
            });
        });
    });

    // Layers
    $router->group([
        'middleware' => ['auth:api', 'scope:layers'],
        'prefix' => 'layers'
    ], function () use ($router) {
        $router->get('/', 'LayersController@index');
        $router->post('/', 'LayersController@store');
        $router->get('/{id}', 'LayersController@show');
        $router->put('/{id}', 'LayersController@update');
        $router->delete('/{id}', 'LayersController@destroy');
    });

    // Media
    $router->group([
        'middleware' => ['auth:api', 'scope:media'],
        'prefix' => 'media'
    ], function () use ($router) {
        $router->get('/', 'MediaController@index');
        $router->post('/', 'MediaController@store');
        $router->get('/{id}', 'MediaController@show');
        $router->put('/{id}', 'MediaController@update');
        $router->delete('/{id}', 'MediaController@destroy');
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
        'middleware' => ['auth:api', 'scope:posts'],
        'namespace' => 'Posts',
        'prefix' => 'posts'
    ], function () use ($router) {
        $router->get('/', 'PostsController@index');
        // @todo stop using this in client, and remove?
        $router->options('/', ['uses' => 'PostsController@indexOptions']);
        $router->post('/', 'PostsController@store');
        $router->get('/{id:[0-9]+}', 'PostsController@show');
        $router->put('/{id:[0-9]+}', 'PostsController@update');
        $router->delete('/{id:[0-9]+}', 'PostsController@destroy');

        // GeoJSON
        $router->get('/geojson', 'GeoJSONController@index');
        $router->get('/geojson/{zoom}/{x}/{y}', 'GeoJSONController@index');
        $router->get('/{id:[0-9]+}/geojson', 'GeoJSONController@show');

        // Locks
        $router->put('/{post_id:[0-9]+}/lock', 'LockController@store');
        $router->delete('/{post_id:[0-9]+}/lock', 'LockController@destroy');

        // Export
        $router->get('/export', 'ExportController@index');

        // Stats
        $router->get('/stats', 'PostsController@stats');

        // Sub-form routes
        $router->group(['prefix' => '/{parent_id:[0-9]+}'], function () use ($router) {
            // Revisions
            $router->group(['prefix' => 'revisions'], function () use ($router) {
                $router->get('/', 'RevisionsController@index');
                $router->get('/{id}', 'RevisionsController@show');
            });

            // Translations
            $router->group(['prefix' => 'translations'], function () use ($router) {
                $router->get('/', 'TranslationsController@index');
                $router->post('/', 'TranslationsController@store');
                $router->get('/{id:[0-9]+}', 'TranslationsController@show');
                $router->put('/{id:[0-9]+}', 'TranslationsController@update');
                $router->delete('/{id:[0-9]+}', 'TranslationsController@destroy');
                $router->get('/{locale:[A-Za-z_]+}', 'TranslationsController@show');
                $router->put('/{locale:[A-Za-z_]+}', 'TranslationsController@update');
                $router->delete('/{locale:[A-Za-z_]+}', 'TranslationsController@destroy');
            });
        });
    });

    // Roles
    $router->group([
        'middleware' => ['auth:api', 'scope:roles'],
        'prefix' => 'roles'
    ], function () use ($router) {
        $router->get('/', 'RolesController@index');
        $router->post('/', 'RolesController@store');
        $router->get('/{id}', 'RolesController@show');
        $router->put('/{id}', 'RolesController@update');
        $router->delete('/{id}', 'RolesController@destroy');
    });

    // Register
    $router->post('/register', 'RegisterController@store');

    // Saved Searches
    $router->group([
        'middleware' => ['auth:api', 'scope:savedsearches'],
        'prefix' => 'savedsearches'
    ], function () use ($router) {
        $router->get('/', 'SavedSearchesController@index');
        $router->post('/', 'SavedSearchesController@store');
        $router->get('/{id}', 'SavedSearchesController@show');
        $router->put('/{id}', 'SavedSearchesController@update');
        $router->delete('/{id}', 'SavedSearchesController@destroy');
    });

    // Tags
    $router->group([
        'middleware' => ['auth:api', 'scope:tags'],
        'prefix' => 'tags'
    ], function () use ($router) {
        $router->get('/', 'TagsController@index');
        $router->post('/', 'TagsController@store');
        $router->get('/{id}', 'TagsController@show');
        $router->put('/{id}', 'TagsController@update');
        $router->delete('/{id}', 'TagsController@destroy');
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
        'middleware' => ['auth:api', 'scope:users'],
        'prefix' => 'users'
    ], function () use ($router) {
        $router->get('/', 'UsersController@index');
        $router->post('/', 'UsersController@store');
        $router->get('/{id:[0-9]+}', 'UsersController@show');
        $router->put('/{id:[0-9]+}', 'UsersController@update');
        $router->delete('/{id:[0-9]+}', 'UsersController@destroy');
        $router->get('/me', 'UsersController@showMe');
        $router->put('/me', 'UsersController@updateMe');
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
    });
});

// Migration
$router->get('/migrate', 'MigrateController@migrate');
