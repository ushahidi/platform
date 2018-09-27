<?php

// Tags
$router->group([
    'prefix' => 'confidence_scores',
    'middleware' => ['scope:tags']
], function () use ($router) {
    // Public access
    resource($router, '/', 'ConfidenceScoresController', [
        'only' => ['index', 'show'],
    ]);

    // Restricted access
    resource($router, '/', 'ConfidenceScoresController', [
        'middleware' => ['auth:api'],
        'only' => ['store', 'update'],
    ]);
});
