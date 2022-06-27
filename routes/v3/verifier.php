<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// $router->resource('verifier/db', 'VerifyController@db');
// $router->resource('verifier/env', 'VerifyController@conf');

$router->group([
    'prefix' => 'verifier',
], function () use ($router) {
    $router->any('/db', 'VerifyController@db');
    $router->any('/env', 'VerifyController@conf');
});
