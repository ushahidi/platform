<?php
/**
 * API version number
 */
$apiVersion = '5';
$apiBase = '/v' . $apiVersion;

$router->group([
    'prefix' => $apiBase,
], function () use ($router) {
    // Forms
    $router->group(
        [
            // 'namespace' => 'Forms',
            'prefix' => 'surveys',
            'middleware' => ['scope:forms', 'expiration']
        ],
        function () use ($router) {
            // Public access
            $router->get('/', 'SurveyController@index');
            $router->get('/{id}', 'SurveyController@show');
            $router->get('/{survey_id}/stats', 'SurveyController@stats');

            $router->group(
                [
                    'prefix' => '{survey_id}/roles',
                    'middleware' => ['scope:users', 'auth:api', 'feature:user-settings', 'expiration']
                ],
                function () use ($router) {
                        $router->get('/', 'SurveyRoleController@index');
                }
            );
        }
    );

    // Restricted access
    $router->group(
        [
            'prefix' => 'surveys',
            'middleware' => ['auth:api', 'scope:forms']
        ],
        function () use ($router) {
            $router->post('/', 'SurveyController@store');
            $router->put('/{id}', 'SurveyController@update');
            $router->delete('/{id}', 'SurveyController@delete');

            $router->group(
                [
                    'prefix' => '{survey_id}/roles',
                    'middleware' => ['scope:users', 'auth:api', 'feature:user-settings', 'expiration']
                ],
                function () use ($router) {
                        $router->put('/', 'SurveyRoleController@replace');
                }
            );
        }
    );


    $router->group(
        [
            'prefix' => 'categories',
            'middleware' => ['scope:tags', 'expiration']
        ],
        function () use ($router) {
            // Public access
            $router->get('/', 'CategoryController@index');
            $router->get('/{id}', 'CategoryController@show');
        }
    );

    // Restricted access
    $router->group(
        [
            'prefix' => 'categories',
            'middleware' => ['auth:api', 'scope:tags']
        ],
        function () use ($router) {
            $router->post('/', 'CategoryController@store');
            $router->put('/{id}', 'CategoryController@update');
            $router->delete('/{id}', 'CategoryController@delete');
        }
    );


    // Restricted access
    $router->group(
        [
            'prefix' => '',
        ],
        function () use ($router) {
            $router->get('/languages', 'LanguagesController@index');
        }
    );

    // Posts
    $router->group(
        [
            'prefix' => 'posts',
            'middleware' => ['scope:posts', 'expiration']
        ],
        function () use ($router) {
            // Public access
            $router->get('/', 'PostController@index');
            $router->get('/{id}', 'PostController@show');
        }
    );

    // Restricted access
    $router->group(
        [
            'prefix' => 'posts',
            'middleware' => ['auth:api', 'scope:posts']
        ],
        function () use ($router) {
            $router->post('/bulk', 'PostController@bulkOperation');
            $router->put('/{id}', 'PostController@update');
            $router->patch('/{id}', 'PostController@patch');
            $router->delete('/{id}', 'PostController@delete');
        }
    );

    $router->group(
        [
            'prefix' => 'posts',
            'middleware' => ['scope:posts']
        ],
        function () use ($router) {
            // Public access
            $router->post('/', 'PostController@store');
            // temporary endpoints, these should eventually go away
            $router->post('/_ussd', 'USSDController@store');
            $router->post('/_whatsapp', 'WhatsAppController@store');
        }
    );

    $router->group([
        'prefix' => 'country-codes',
        'middleware' => ['auth:api', 'scope:country_codes'],
    ], function () use ($router) {
        $router->get('/', 'CountryCodeController@index');
        $router->get('/{id}', 'CountryCodeController@show');
    });
    
    /* Users */
    // Restricted access
    $router->group(
        [
            'prefix' => 'users',
            'middleware' => ['scope:users', 'auth:api', 'expiration']
        ],
        function () use ($router) {
            $router->get('/me', 'UserController@showMe');
            $router->put('/me', 'UserController@updateMe');
        }
    );


    // Public access
    $router->group(
        [
            'prefix' => 'users',
            'middleware' => ['scope:users', 'expiration'],
        ],
        function () use ($router) {
            $router->get('/', 'UserController@index');
            $router->get('/{id}', 'UserController@show');
        }
    );

    // Restricted access
    $router->group(
        [
            'prefix' => 'users',
            'middleware' => ['scope:users', 'auth:api', 'expiration']
        ],
        function () use ($router) {
            $router->post('/', 'UserController@store');
            $router->put('/{id}', 'UserController@update');
            $router->delete('/{id}', 'UserController@delete');

            $router->group(
                [
                    'prefix' => '{user_id}/settings',
                    'middleware' => ['scope:users', 'auth:api', 'feature:user-settings', 'expiration']
                ],
                function () use ($router) {
                        $router->get('/', 'UserSettingController@index');
                        $router->get('/{id}', 'UserSettingController@show');
                        $router->post('/', 'UserSettingController@store');
                        $router->put('/{id}', 'UserSettingController@update');
                        $router->delete('/{id}', 'UserSettingController@delete');
                }
            );
        }
    );


    // Permissions
    $router->group(
        [
            'prefix' => 'permissions',
            'middleware' => ['auth:api', 'scope:tos', 'expiration']
        ],
        function () use ($router) {
            $router->get('/', 'PermissionsController@index');
            $router->get('/{id}', 'PermissionsController@show');
        }
    );
    /* Roles */
    // Public access
    $router->group(
        [
            'prefix' => 'roles',
            'middleware' => ['scope:roles', 'expiration']
        ],
        function () use ($router) {
            $router->get('/', 'RoleController@index');
            $router->get('/{id}', 'RoleController@show');
        }
    );

    // Restricted access
    $router->group(
        [
            'prefix' => 'roles',
            'middleware' => ['auth:api', 'scope:roles']
        ],
        function () use ($router) {
            $router->post('/', 'RoleController@store');
            $router->put('/{id}', 'RoleController@update');
            $router->delete('/{id}', 'RoleController@delete');
        }
    );

    // Restricted access
    $router->group(
        [
            'prefix' => 'tos',
            'middleware' => ['auth:api', 'scope:tos']
        ],
        function () use ($router) {
            $router->get('/', 'TosController@index');
            $router->get('/{id}', 'TosController@show');
            $router->post('/', 'TosController@store');
        }
    );



    $router->group(
        [
            'prefix' => 'savedsearches',
            'middleware' => ['scope:savedsearches']
        ],
        function () use ($router) {
            $router->get('/', 'SavedSearchController@index');
            $router->get('/{id}', 'SavedSearchController@show');
        }
    );

    $router->group(
        [
            'prefix' => 'savedsearches',
            'middleware' => ['scope:savedsearches', 'auth:api', 'expiration']
        ],
        function () use ($router) {
            $router->post('/', 'SavedSearchController@store');
            $router->put('/{id}', 'SavedSearchController@update');
            $router->delete('/{id}', 'SavedSearchController@delete');
        }
    );

    $router->group(
        [
            'prefix' => 'collections',
            'middleware' => ['scope:collections,sets']
        ],
        function () use ($router) {
            $router->get('/', 'CollectionController@index');
            $router->get('/{id}', 'CollectionController@show');
            $router->group(
                [
                    'prefix' => '{collection_id}/posts',
                    'middleware' => ['scope:collections,sets',  'expiration']
                ],
                function () use ($router) {
                        $router->get('/', 'CollectionPostController@index');
                        $router->get('/{id}', 'CollectionPostController@show');
                }
            );
        }
    );

    $router->group(
        [
            'prefix' => 'collections',
            'middleware' => ['scope:collections,sets', 'auth:api', 'expiration']
        ],
        function () use ($router) {
            $router->post('/', 'CollectionController@store');
            $router->put('/{id}', 'CollectionController@update');
            $router->delete('/{id}', 'CollectionController@delete');

            $router->group(
                [
                    'prefix' => '{collection_id}/posts',
                    'middleware' => ['scope:collections,sets', 'auth:api', 'expiration']
                ],
                function () use ($router) {
                        $router->post('/', 'CollectionPostController@store');
                        //$router->put('/{id}', 'CollectionPostController@update');
                        $router->delete('/{id}', 'CollectionPostController@delete');
                }
            );
        }
    );
});
