<?php

// Tags
$router->group([
    'prefix' => 'tags',
    'middleware' => ['scope:tags']
], function () use ($router) {
    // Public access
    $router->get('/', 'TagsController@index');
    $router->get('/{id}', 'TagsController@show');

    // Restricted access
    $router->group([
        'middleware' => ['auth:api', 'scope:tags']
    ], function () use ($router) {
        $router->post('/', 'TagsController@store');
        $router->put('/{id}', 'TagsController@update');
        $router->delete('/{id}', 'TagsController@destroy');
    });
});
