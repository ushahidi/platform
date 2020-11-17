<?php
/**
 * Posts Config
 */

return [
    // Max number of posts that can be fetched for privileged/unprivileged requests
    'list_admin_max_limit' => env('API_POST_LIST_ADMIN_MAX_LIMIT', 100),
    'list_max_limit' => env('API_POST_LIST_MAX_LIMIT', 20),
];
