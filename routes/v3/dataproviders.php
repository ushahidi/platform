<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// Data providers
$router->resource('dataproviders', 'DataProvidersController', [
    'middleware' => ['auth:api', 'scope:dataproviders', 'expiration'],
    'only' => ['index', 'show'],
    'id' => 'id', // Override id to allow non-numeric IDs
    'parameters' => ['dataproviders' => 'id'],
]);
