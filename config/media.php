<?php
/**
 * Media Config
 */

return [

    // Maximum file upload size in bytes. Remember this figure should not be larger
    // than the maximum file upload size set on the server. 1Mb by default.
    'max_upload_bytes' => getenv('MEDIA_MAX_UPLOAD') ?: '10485760',
];
