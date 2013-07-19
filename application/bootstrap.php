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
if (isset($_SERVER['KOHANA_ENV']))
{
	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
Kohana::init(array(
	'base_url'   => '/',
	'index_file' => FALSE
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	'auth'       => MODPATH.'auth',       // Basic authentication
	'cache'      => MODPATH.'cache',      // Caching with multiple backends
	// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	'database'   => MODPATH.'database',   // Database access
	'image'      => MODPATH.'image',      // Image manipulation
	'orm'        => MODPATH.'orm',        // Object Relationship Mapping
	'unittest'   => MODPATH.'unittest',   // Unit testing
	'minion'     => MODPATH.'minion',
	'migrations' => MODPATH.'migrations',
	'koauth'     => MODPATH.'koauth',
	'media'      => MODPATH.'media',
	'ushahidiui' => MODPATH.'UshahidiUI',
	// 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
	));

/**
 * Set cookie salt
 * @TODO change this for your project
 */
Cookie::$salt = 'ushahidi-insecure-please-change-me';

// Load gisconverter
$gisconverter = Kohana::find_file('vendor', 'gisconverter/gisconverter', 'php');
if (! $gisconverter) throw new Kohana_Exception('Could not load gisconverter library. Have you checked out the gisconverter submodule?');
include($gisconverter);

/**
 * Form Groups API SubRoute
 */	
Route::set('form-groups', 'api/v2/forms/<form_id>/groups/<group_id>/<controller>(/<id>)', 
	array(
		'form_id' => '\d+',
		'group_id' => '\d+',
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api/Forms/Groups'
	));

/**
 * Forms API SubRoute
 */	
Route::set('forms', 'api/v2/forms/<form_id>/<controller>(/<id>)', 
	array(
		'form_id' => '\d+',
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api/Forms'
	));

/**
 * GeoJSON API SubRoute
 */	
Route::set('geojson', 'api/v2/posts/geojson(/<zoom>/<x>/<y>)', 
	array(
		'zoom' => '\d+',
		'x' => '\d+',
		'y' => '\d+',
	))
	->defaults(array(
		'action'     => 'index',
		'controller' => 'GeoJSON',
		'directory'  => 'Api/Posts'
	));

/**
 * GeoJSON API SubRoute
 */	
Route::set('geojson-post-id', 'api/v2/posts/<id>/geojson', 
	array(
		'id' => '\d+',
		'zoom' => '\d+',
		'x' => '\d+',
		'y' => '\d+',
	))
	->defaults(array(
		'action'     => 'index',
		'controller' => 'GeoJSON',
		'directory'  => 'Api/Posts'
	));

/**
 * Posts API SubRoute
 */	
Route::set('posts', 'api/v2/posts/<post_id>/<controller>(/<id>)', 
	array(
		'post_id' => '\d+',
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api/Posts'
	));

/**
 * Base Ushahidi API Route
 */
Route::set('api', 'api/v2(/<controller>(/<id>))', 
	array(
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api'
	));

/**
 * Translations API SubRoute
 */	
Route::set('translations', 'api/v2/posts/<post_id>/translations(/<locale>)', 
	array(
		'post_id' => '\d+',
		'locale' => '[a-zA-Z_]+'
	))
	->defaults(array(
		'action'     => 'index',
		'controller' => 'Translations',
		'directory'  => 'Api/Posts'
	));

/**
 * Default Route
 */
Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'ushahidi',
		'action'     => 'index',
	));
