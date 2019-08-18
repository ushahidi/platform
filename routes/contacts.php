<?php

// Contacts
resource($router, 'contacts', 'ContactsController', [
    'middleware' => ['auth:api', 'scope:contacts', 'expiration']
]);
