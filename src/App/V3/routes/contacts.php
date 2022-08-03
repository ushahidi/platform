<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// Contacts
$router->resource('contacts', 'ContactsController', [
    'except' => ['create', 'edit'],
    'middleware' => ['auth:api', 'scope:contacts', 'expiration'],
    'parameters' => ['contacts' => 'id'],
]);
