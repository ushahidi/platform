<?php

// Export Jobs
resource($router, '/exports/jobs', 'Exports\JobsController', [
    'middleware' => ['auth:api', 'scope:posts'],
]);
