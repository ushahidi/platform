<?php defined('SYSPATH') OR die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/Kohana/Core'.EXT;

if (is_file(APPPATH.'classes/Kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/Kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/Kohana'.EXT;
}

/**
 * Set the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
date_default_timezone_set('UTC');

/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (($env = getenv('KOHANA_ENV')) !== FALSE)
{
	/**
	 * We have to ignore this line in the coding standards because it expects
	 * constants to always be uppercase.
	 *
	 * The error that is returned from PHPCS is:
	 * Constants must be uppercase; expected 'KOHANA::' but found 'Kohana::'
	 */
	// @codingStandardsIgnoreStart
	Kohana::$environment = constant('Kohana::'.strtoupper($env));
	// @codingStandardsIgnoreEnd
}
else
{
	$env = 'development';
	Kohana::$environment = Kohana::DEVELOPMENT;
}

/**
 * Attach a file reader to config. Multiple readers are supported.
 */

Kohana::$config = new Config;
Kohana::$config->attach(new Config_File);

/**
 * Attach the environment specific configuration file reader to config if not in production.
 */
if (Kohana::$environment != Kohana::PRODUCTION)
{
	Kohana::$config->attach(new Config_File('config/environments/'.$env));
}

/**
 * Initialize Kohana, setting the default options.
 */
Kohana::init(Kohana::$config->load('init')->as_array());

// Set up custom error view
Kohana_Exception::$error_view_content_type = 'application/json';
Kohana_Exception::$error_view = 'api/error';

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(Kohana::$config->load('modules')->as_array());

/**
 * Cookie salt is used to make sure cookies haven't been modified by the client
 * @TODO: Change this for each project
 */
Cookie::$salt = 'KEVEHQxU;CfHY32LbpHn(c(uctcexPjA';

/**
 * Include default routes. Default routes are located in application/routes/default.php
 */
include Kohana::find_file('routes', 'default');

/**
 * Include the routes for the current environment.
 */

if ($routes = Kohana::find_file('routes', Kohana::$environment))
{
	include $routes;
}
