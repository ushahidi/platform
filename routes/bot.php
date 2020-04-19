<?php

// bots
$router->group([
    'namespace' => 'Bot',
    'prefix' => 'bot',
    'middleware' => 'verifybot'
], function () use ($router) {
    // public access
    $router->get('/', 'BotController@index');
});
