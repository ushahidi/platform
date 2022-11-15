<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// TOS
$router->resource('tos', 'TosController', [
    'middleware' => ['auth:api', 'scope:tos'],
    'only' => ['index', 'store', 'show'],
    'parameters' => ['tos' => 'id'],
]);
