<?php

// Country Codes
resource($router, 'country-codes', 'CountryCodesController', [
    'middleware' => ['auth:api', 'scope:country_codes'],
    'only' => ['index', 'show']
]);
