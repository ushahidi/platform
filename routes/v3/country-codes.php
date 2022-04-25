<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// Country Codes
$router->resource('country-codes', 'CountryCodesController', [
    'middleware' => ['auth:api', 'scope:country_codes'],
    'only' => ['index', 'show'],
    'parameters' => ['country-codes' => 'id'],
]);
