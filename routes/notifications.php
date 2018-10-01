<?php

// Notifications
resource($router, 'notifications', 'NotificationsController', [
    'middleware' => ['auth:api', 'scope:notifications', 'expiration'],
]);
