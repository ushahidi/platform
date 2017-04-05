<?php

// Initialize the Kohana application
require __DIR__ . '/../application/kohana.php';

/**
 * Some hosters set $_SERVER['ORIG_PATH_INFO'] instead of the more standard
 * $_SERVER['PATH_INFO'], we check for that here. Otherwise, let Kohana try to
 * automatically detect the URI.
 */
$server_path = TRUE;
if (array_key_exists('ORIG_PATH_INFO', $_SERVER)) {
  $server_path = $_SERVER['ORIG_PATH_INFO'];
}
/**
 * Execute the main request. Use the URI passed in the first parameter, if not specified
 * (defaults to TRUE), we will try to automatically detect the URI.
 */
echo Request::factory($server_path, array(), FALSE)
	->execute()
	->send_headers(TRUE)
	->body();
