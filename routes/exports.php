<?php

// Export Jobs
$router->group([
    'namespace' => 'Exports',
    'middleware' => ['auth:api', 'scope:posts'],
    'prefix' => '/exports/jobs'
], function () use ($router) {
    $router->get('/', 'JobsController@index');
    $router->post('/', 'JobsController@store');
    $router->get('/{id}', 'JobsController@show');
    $router->put('/{id}', 'JobsController@update');
    $router->delete('/{id}', 'JobsController@destroy');
});

$router->group([
    'namespace' => 'Exports\External',
    'middleware' => ['signature'],
    'prefix' => '/exports/external'
], function () use ($router) {
    // External jobs
    $router->get('/jobs', 'JobsController@index');
    $router->get('/jobs/{id:[0-9]+}', 'JobsController@show');
    $router->put('/jobs/{id:[0-9]+}', 'JobsController@update');

    // Count export
    $router->get('/count/{id:[0-9]+}', 'CountController@show');

    // Run CLI for export
    // @todo this should not be a get
    $router->get('/cli/{id:[0-9]+}', 'CliController@show');
});
