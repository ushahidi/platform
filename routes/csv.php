<?php

// CSV + Import
$router->group([
    'middleware' => ['auth:api', 'scope:csv'],
    'namespace' => 'CSV',
    'prefix' => 'csv'
], function () use ($router) {
    resource($router, '/', 'CSVController', [
        'middleware' => ['auth:api', 'scope:csv', 'expiration']
    ]);

    $router->post('/{id}/import', 'CSVImportController@store');
});
