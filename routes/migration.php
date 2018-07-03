<?php

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
