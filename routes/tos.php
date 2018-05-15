<?php

// TOS
resource($router, 'tos', 'TosController', [
    'middleware' => ['auth:api', 'scope:tos'],
    'only' => ['index', 'store', 'show'],
]);
