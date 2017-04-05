<?php

require_once __DIR__.'/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

// Initialize the Kohana application
require __DIR__ . '/../application/kohana.php';

$app = require __DIR__.'/lumen.php';

return $app;
