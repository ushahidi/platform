<?php

// Set the full path to the docroot
define('DOCROOT', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

// Initialize the Kohana application
require __DIR__ . '/../application/kohana.php';

// this is to get the stats when visiting http://192.168.33.110/. There is probably a better way to access the stats...
// echo View::factory('profiler/stats');
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
}
