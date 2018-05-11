<?php

// Country Codes
$router->group([
    'middleware' => ['auth:api', 'scope:country_codes'],
    'prefix' => 'country-codes'
], function () use ($router) {
    $router->get('/', 'CountryCodesController@index');
    $router->get('/{id}', 'CountryCodesController@show');
});
