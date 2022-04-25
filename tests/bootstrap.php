<?php
// @codingStandardsIgnoreFile

// Load Aura
require_once __DIR__.'/../bootstrap/init.php';

// Bootstrap laravel
require __DIR__.'/../bootstrap/app.php';

// Disable output buffering
if (($ob_len = ob_get_length()) !== false) {
    // flush_end on an empty buffer causes headers to be sent. Only flush if needed.
    if ($ob_len > 0) {
        ob_end_flush();
    } else {
        ob_end_clean();
    }
}
