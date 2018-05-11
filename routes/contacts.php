<?php

// Contacts
$router->group([
    'middleware' => ['auth:api', 'scope:contacts'],
    'prefix' => 'contacts'
], function () use ($router) {
    $router->get('/', 'ContactsController@index');
    $router->post('/', 'ContactsController@store');
    $router->get('/{id}', 'ContactsController@show');
    $router->put('/{id}', 'ContactsController@update');
    $router->delete('/{id}', 'ContactsController@destroy');
});
