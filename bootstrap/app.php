<?php

require_once __DIR__.'/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}


// Ushahidi: load transitional code
require_once __DIR__.'/../src/Init.php';

$app = require __DIR__.'/lumen.php';

return $app;
