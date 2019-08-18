<?php
/**
 * Media Config
 */

return [

    // Maximum file upload size in bytes. Remember this figure should not be larger
    // than the maximum file upload size set on the server. 1Mb by default.
    'max_upload_bytes' => env('MEDIA_MAX_UPLOAD', '10485760'),

    // Lifespan of temporary URLs returned in export_job api
    'temp_url_lifespan' => env('MEDIA_TEMP_URL_LIFESPAN', '1 hour'),

    // Path prefix for final csv output
    'csv_final_prefix' => 'csv',
    // Path prefix for csv batch output
    'csv_batch_prefix' => 'csv/batches',
    // csv batch size
    'csv_batch_size' => env('MEDIA_CSV_BATCH_SIZE', 200),
];
