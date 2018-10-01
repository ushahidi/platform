<?php

resource($router, 'apikeys', 'ApiKeysController', [
    'middleware' => ['auth:api', 'scope:apikeys', 'expiration'],
]);
