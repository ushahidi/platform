<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// Password reset
$router->post('/passwordreset', 'PasswordResetController@store');
$router->post('/passwordreset/confirm', 'PasswordResetController@confirm');

// Register
$router->post('/register', 'RegisterController@store');
