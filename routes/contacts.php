<?php

// Contacts
resource($router, 'contacts', 'ContactsController', [
    'as' => "contacts",
    'middleware' => ['auth:api', 'scope:contacts', 'expiration']
]);
