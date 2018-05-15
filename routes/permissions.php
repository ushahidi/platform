<?php

// Permissions
resource($router, 'permissions', 'PermissionsController', [
    'middleware' => ['auth:api', 'scope:permissions'],
    'only' => ['index', 'show'],
]);
