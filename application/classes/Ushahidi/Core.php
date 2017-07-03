<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Core
 *
 * Handle plugin loading
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

abstract class Ushahidi_Core {

	/**
	 * Initializes Ushahidi and Plugins
	 */
	public static function init()
	{
		/**
		 * 0. Register depenendencies for injection.
		 */
		$di = service();

		// Kohana injection
		// DB config
		$di->set('db.config', function() use ($di) {
			$config = Kohana::$config->load('database')->default;

			// Is this a multisite install?
			$multisite = config('multisite.enabled');
			if ($multisite) {
				$config = $di->get('multisite')->getDbConfig();
			}

			return $config;
		});
		// Multisite db
		$di->set('kohana.db.multisite', function () use ($di) {
			return Database::instance('multisite');
		});
		// Deployment db
		$di->set('kohana.db', function() use ($di) {
			return Database::instance('deployment', $di->get('db.config'));
		});

		// Intercom config settings
		$di->set('thirdparty.intercomAppToken', function() use ($di) {
			return getenv('INTERCOM_APP_TOKEN');
		});

		$di->set('tool.validation', $di->lazyNew('Ushahidi_ValidationEngine'));

		$di->set('tool.mailer', $di->lazyNew('Ushahidi_Mailer', [
			'siteConfig' => $di->lazyGet('site.config'),
			'clientUrl' => $di->lazyGet('clienturl')
		]));

		/**
		 * 1. Load the plugins
		 */
		self::load();

		/**
		 * Attach database config
		 */
		// self::attached_db_config();
	}

	public static function attached_db_config()
	{
		// allowed groups are stored with the config service.
		$groups = service('repository.config')->groups();

		$db = service('kohana.db');

		/**
		 * Attach database config to override some settings
		 */
		try
		{
			if (DB::query(Database::SELECT, 'SHOW TABLES LIKE \'config\'')->execute($db)->count() > 0)
			{
				Kohana::$config->attach(new Ushahidi_Config([
					'groups' => $groups,
					'instance' => $db
				]));
			}
		}
		catch (Exception $e)
		{
			// Ignore errors if database table doesn't exist yet
		}
	}

	/**
	 * Load All Plugins Into System
	 */
	public static function load()
	{
		if (! defined('PLUGINPATH') OR ! is_dir(PLUGINPATH)) return;

		// Load Plugins
		$results = scandir(PLUGINPATH);
		foreach ($results as $result)
		{
			if ($result === '.' or $result === '..') continue;

			if (is_dir(PLUGINPATH.$result))
			{
				Kohana::modules( array($result => PLUGINPATH.$result) + Kohana::modules() );
			}
		}
	}

	/**
	 * Useful for development. Only intended for temporary debugging.
	 */
	public static function log(/* anything */)
	{
		$message = '';
		foreach (func_get_args() as $arg) {
			$message .= (is_string($arg) ? $arg : var_export($arg, true)) . "\n";
		}

		$log = \Log::instance();
		$log->add(Log::INFO, $message);
	}
}
