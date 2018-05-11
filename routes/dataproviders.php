<?php

// Data providers
$router->group([
    'middleware' => ['auth:api', 'scope:dataproviders'],
    'prefix' => 'dataproviders'
], function () use ($router) {
    $router->get('/', 'DataProvidersController@index');
    $router->get('/{id}', 'DataProvidersController@show');
});
