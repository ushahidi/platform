<?php

// Data providers
resource($router, 'dataproviders', 'DataProvidersController', [
    'middleware' => ['auth:api', 'scope:dataproviders', 'expiration'],
    'only' => ['index', 'show'],
    'id' => 'id' // Override id to allow non-numeric IDs
]);
