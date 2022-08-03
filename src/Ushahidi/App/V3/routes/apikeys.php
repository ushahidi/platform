<?php
/**
 *  @var $router \Illuminate\Routing\Router
 */

$router->resource('apikeys', 'ApiKeysController', [
    'middleware' => ['auth:api', 'scope:apikeys', 'expiration'],
    'parameters' => ['apikeys' => 'id'],
]);
