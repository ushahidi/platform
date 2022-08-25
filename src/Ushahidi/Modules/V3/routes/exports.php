<?php

// Export Jobs
$router->resource('/exports/jobs', 'Exports\JobsController', [
    'middleware' => ['auth:api', 'scope:posts'],
    'parameters' => ['jobs' => 'id'],
    'except' => ['create', 'edit'],
]);
