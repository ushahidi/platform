<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Ushahidi Default Routes
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Config
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

/**
 * API version number
 */
$apiVersion = '3';
$apiBase = 'api/v' . $apiVersion . '/';

/**
 * Custom media router.
 */
Route::set('media', 'media/<filepath>', array(
		'filepath' => '.*', // Pattern to match the file path
	))
	->defaults(array(
		'controller' => 'Media',
		'action'     => 'serve',
	));

/**
 * Path to CSV uploads.
 */
Route::set('csv', $apiBase . 'csv(/<id>)',  array(
		'id' => '\d+'
	))
	->defaults(array(
		'controller' => 'CSV',
		'action'     => 'index',
		'directory'  => 'Api'
	));

/**
 * Path to CSV imports.
 */
Route::set('csv-import', $apiBase . 'csv/<csv_id>/import',  array(
		'csv_id' => '\d+'
	))
	->defaults(array(
		'controller' => 'Import',
		'action'     => 'index',
		'directory'  => 'Api/CSV'
	));

/**
 * Set Posts API SubRoute
 */
Route::set('collections-posts', $apiBase . 'collections/<set_id>/posts(/<id>)',
	array(
		'set_id' => '\d+',
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'controller' => 'Posts',
		'directory'  => 'Api/Collections'
	));

/**
 * Stats Posts API SubRoute
 */
Route::set('post-stats', $apiBase . 'posts/stats')
	->defaults(array(
		'action'     => 'stats',
		'controller' => 'Posts',
		'directory'  => 'Api'
	));

/**
 * Lock Posts API SubRoute
 */
Route::set('post-lock', $apiBase . 'posts(/<post_id>)/lock(/<lock_id>)',
	array(		
		'post_id' => '\d+',
		'lock_id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'controller' => 'Lock',
		'directory'  => 'Api/Posts'
	));	
	

/**
 * GeoJSON API SubRoute
 */
Route::set('geojson', $apiBase . 'posts/geojson(/<zoom>/<x>/<y>)',
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
Route::set('geojson-post-id', $apiBase . 'posts/<id>/geojson',
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
Route::set('posts', $apiBase . 'posts/<parent_id>/<controller>(/<id>)',
	array(
		'parent_id' => '\d+',
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api/Posts'
	));

/**
 * Base Ushahidi API Route
 */
Route::set('current-user', $apiBase . 'users/me')
	->defaults(array(
		'action'     => 'me',
		'directory'  => 'Api',
		'controller' => 'Users',
		'id'         => 'me'
	));

/**
 * Password Reset Route
 */
Route::set('passwordreset-api', $apiBase . 'passwordreset(/<action>)', [
		'action' => '(?:index|confirm)'
	])
	->defaults([
			'action'     => 'index',
			'directory'  => 'Api',
			'controller' => 'PasswordReset',
	]);

/**
 * Config API Route
 */
Route::set('config-api', $apiBase . 'config(/<id>(/<key>))',
	array(
		'id' => '[a-zA-Z_-]+',
		'key' => '[a-zA-Z_.-]+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api',
		'controller' => 'Config',
	));

/**
 * Messages API Route
 */
Route::set('messages-api', $apiBase . 'messages(/<id>(/<action>))',
	array(
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api',
		'controller' => 'Messages'
	));

/**
 * Dataproviders API Route
 */
Route::set('dataproviders-api', $apiBase . 'dataproviders(/<id>)',
	array(
		'id' => '[a-zA-Z_-]+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api',
		'controller' => 'DataProviders',
	));

/**
 * SavedSearches API Route
 */
Route::set('savedsearches-api', $apiBase . 'savedsearches(/<id>)',
	array(
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api',
		'controller' => 'SavedSearches',
	));

/**
 * Post stats API route
 */
// Route::set('post-stats-api', $apiBase . 'stats/posts')
// 	->defaults(array(
// 		'action'     => 'index',
// 		'directory'  => 'Api/Stats',
// 		'controller' => 'Posts',
// 	));

/**
 * Base Ushahidi API Route
 */
Route::set('api', $apiBase . '(<controller>(/<id>))',
	array(
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api'
	));

/**
 * Forms API SubRoute
 */
Route::set('forms', $apiBase . 'forms(/<form_id>)/<controller>(/<id>)',
	array(
		'form_id' => '\d+',
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api/Forms'
	));


/**
 * Translations API SubRoute
 */
Route::set('translations', $apiBase . 'posts/<parent_id>/translations(/<locale>)',
	array(
		'parent_id' => '\d+',
		'locale' => '[a-zA-Z_]+'
	))
	->defaults(array(
		'action'     => 'index',
		'controller' => 'Translations',
		'directory'  => 'Api/Posts'
	));

/**
 * Migration Route
 */
Route::set('migration', $apiBase . 'migration/<action>',
	array(
		'action' => '(?:status|rollback|migrate)',
	))
	->defaults(array(
		'action'     => 'rollback',
		'controller' => 'Migration',
		'directory'  => 'Api'
	));

/**
 * Webhook API Route
 */
Route::set('inbound-webhook-api', $apiBase . 'webhooks/<controller>/(<id>)',
	array(
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api/Webhooks/'
	));

/**
 * Export Job External API Route
 */
Route::set('external-export-job', $apiBase . 'exports/external/<controller>(/<id>)', array())
->defaults(array(
	'action'     => 'index',
	'directory'  => 'Api/Exports/External/'
));

/**
 * Export Job External API Route
 */
Route::set('export-job', $apiBase . 'exports/<controller>(/<id>)',
	array(
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api/Exports/'
	));



/**
 * Migration migrate Route
 */
Route::set('migration-migrate', 'migrate')
	->defaults(array(
		'controller' => 'Migrate'
	));

/**
 * OAuth Route
 * Have to add this manually because the class is OAuth not Oauth
 */
Route::set('oauth', 'oauth(/<action>)',
	array(
		'action' => '(?:index|authorize|token)',
	))
	->defaults(array(
			'controller' => 'OAuth',
			'action'     => 'index',
	));


/**
 * Default Route
 */
Route::set('default', '('.$apiBase.')')
	->defaults(array(
		'controller' => 'Index',
		'action'     => 'index',
		'directory'  => 'Api'
	));
