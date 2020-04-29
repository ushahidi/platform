<?php
/**
 * API version number
 */
$apiVersion = '4';
$apiBase = 'api/v' . $apiVersion;

$router->group([
    'prefix' => $apiBase,
], function () use ($router) {
    // Forms
    $router->group([
        // 'namespace' => 'Forms',
        'prefix' => 'surveys',
        // 'middleware' => ['scope:forms', 'expiration']
    ], function () use ($router) {
        // Public access
        $router->get('/', 'SurveyController@index');
    });
});
