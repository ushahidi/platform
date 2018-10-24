<?php

// Permissions
resource($router, 'permissions', 'PermissionsController', [
    'middleware' => ['auth:api', 'scope:permissions', 'expiration'],
    'only' => ['index', 'show'],
]);
