<?php
// @codingStandardsIgnoreFile
use Illuminate\Contracts\Console\Kernel;

// Bootstrap laravel
$app = require __DIR__.'/../bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

// Disable output buffering
if (($ob_len = ob_get_length()) !== false) {
    // flush_end on an empty buffer causes headers to be sent. Only flush if needed.
    if ($ob_len > 0) {
        ob_end_flush();
    } else {
        ob_end_clean();
    }
}
