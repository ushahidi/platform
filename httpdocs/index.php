<?php

// Set the full path to the docroot
define('DOCROOT', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

// Initialize the Kohana application
require __DIR__ . '/../application/kohana.php';

if (!empty($_GET['install']) && is_file(__DIR__ . '/install.php'))
{
	require __DIR__ . '/install.php';
	exit(1);
}

if (PHP_SAPI == 'cli') // Try and load minion
{
	class_exists('Minion_Task') OR die('Please enable the Minion module for CLI support.');
	set_exception_handler(array('Minion_Exception', 'handler'));

	Minion_Task::factory(Minion_CLI::options())->execute();
}
else
{	
	/* You may set SERVER_PATHINFO_VAR in your .env file with the name of the $_SERVER array
	 * key, through which your host is passing the request URI to this script. For instance,
	 * some hosters clear $_SERVER['PATH_INFO'] and set $_SERVER['ORIG_PATH_INFO'] instead.
	 * By setting SERVER_PATHINFO_VAR to "ORIG_PATH_INFO" you would work around this.
	 */
	$server_path = TRUE;
	if (array_key_exists('SERVER_PATHINFO_VAR', $_ENV)) {
	  $server_path = $_SERVER[$_ENV['SERVER_PATHINFO_VAR']];
	}
	/**
	 * Execute the main request. Use the URI passed in the first parameter, if not specified
	 * (defaults to TRUE), we will try to automatically detect the URI.
	 */	echo Request::factory($server_path, array(), FALSE)
		->execute()
		->send_headers(TRUE)
		->body();
}
