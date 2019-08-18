<?php

// HXL
$router->group([
    'prefix' => 'hxl',
    'middleware' => ['auth:api', 'feature:hxl'],
    'namespace' => 'HXL'
], function () use ($router) {
    $router->get('/', "HXLController@index");
    $router->get('/licenses', 'HXLLicensesController@index');
    $router->get('/tags', 'HXLTagsController@index');
    $router->post('/metadata', 'HXLMetadataController@store');
    $router->get('/metadata', 'HXLMetadataController@index');
    $router->get('/organisations', 'HXLOrganisationsController@index');
});
