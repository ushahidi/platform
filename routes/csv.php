<?php

// CSV + Import
$router->group([
    'middleware' => ['auth:api', 'scope:csv'],
    'namespace' => 'CSV',
    'prefix' => 'csv'
], function () use ($router) {
    $router->get('/', 'CSVController@index');
    $router->post('/', 'CSVController@store');
    $router->get('/{id}', 'CSVController@show');
    $router->put('/{id}', 'CSVController@update');
    $router->delete('/{id}', 'CSVController@destroy');

    $router->post('/{id}/import', 'CSVImportController@store');
});
