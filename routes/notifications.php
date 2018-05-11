<?php

// Notifications
$router->group([
    'middleware' => ['auth:api', 'scope:notifications'],
    'prefix' => 'notifications'
], function () use ($router) {
    $router->get('/', 'NotificationsController@index');
    $router->post('/', 'NotificationsController@store');
    $router->get('/{id}', 'NotificationsController@show');
    $router->put('/{id}', 'NotificationsController@update');
    $router->delete('/{id}', 'NotificationsController@destroy');
});
