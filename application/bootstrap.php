<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Ushahidi Bootstrap
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

// -- Environment setup --------------------------------------------------------

/**
 * Load the core Kohana class
 */
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
 * Add response message for HTTP 422
 */
Kohana_Response::$messages[422] = 'Unprocessable Entity';

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name 'development' will be used instead
 */
if (($env = getenv('KOHANA_ENV')) === FALSE OR defined('Kohana::'.strtoupper($env)) === FALSE)
{
	$env = 'development';
}

// Ignoring code standards error about constant case
// @codingStandardsIgnoreStart
Kohana::$environment = constant('Kohana::'.strtoupper($env));
// @codingStandardsIgnoreEnd

/**
 * Attach a file reader to config. Multiple readers are supported.
 */

Kohana::$config = new Config;
Kohana::$config->attach(new Config_File);

/**
 * Attach the environment specific configuration file reader to config
 */
Kohana::$config->attach(new Config_File('config/environments/'.$env));

/**
 * Initialize Kohana, setting the default options.
 */
Kohana::init(Kohana::$config->load('init')->as_array());

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(Kohana::$config->load('modules')->as_array());

/**
 * Set cookie salt
 * @TODO change this for your project
 */
Cookie::$salt = 'ushahidi-insecure-please-change-me';

/**
 * If the RAVEN_URL is defined, set up raven error logging
 */
if (getenv("RAVEN_URL"))
{
	$client = (new Raven_Client(getenv("RAVEN_URL"), ['exclude' => ['HTTP_Exception_404']]))->install();

	Kohana::$log->attach(new Log_Raven($client));
}

/**
 * Initialize Ushahidi, setting the defaults
 * Note: We have to do this before routing kicks in, so that the 'default' route doesn't catch any custom plugin routes.
 */
Ushahidi::init();

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
