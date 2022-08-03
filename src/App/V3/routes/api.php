<?php
/**
 * API version number
 */
$apiVersion = '3';
$apiBase = '/v'. $apiVersion;

$router->get($apiBase, "API\IndexController@index");
$router->group([
    'prefix' => $apiBase,
    'namespace' => 'API',
], function ($router) {
    require __DIR__. '/index.php';
});
