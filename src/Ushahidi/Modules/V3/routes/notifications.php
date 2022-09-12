<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// Notifications
$router->resource('notifications', 'NotificationsController', [
    'middleware' => ['auth:api', 'scope:notifications', 'expiration'],
    'parameters' => ['notifications' => 'id'],
]);
