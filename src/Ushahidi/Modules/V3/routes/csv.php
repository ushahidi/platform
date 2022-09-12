<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// CSV + Import
$router->group([
    'middleware' => ['auth:api', 'scope:csv'],
    'namespace' => 'CSV',
], function () use ($router) {
    $router->resource('csv', 'CSVController', [
        'middleware' => ['expiration'],
        'parameters' => ['csv' => 'id'],
    ]);

    $router->post('csv/{id}/import', 'CSVImportController@store');
});
