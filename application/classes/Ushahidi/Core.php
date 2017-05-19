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
			$multisite = Kohana::$config->load('multisite.enabled');
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
		// Multisite db
		$di->set('site', function () use ($di) {
			$site = '';

			// Is this a multisite install?
			$multisite = Kohana::$config->load('multisite.enabled');
			if ($multisite) {
				$site = $di->get('multisite')->getSite();
			}

			return $site;
		});

		// CDN Config settings
		$di->set('cdn.config', function() use ($di) {
			return Kohana::$config->load('cdn')->as_array();
		});

		// Ratelimiter config settings
		$di->set('ratelimiter.config', function() use ($di) {
			return Kohana::$config->load('ratelimiter')->as_array();
		});

		// Private deployment config settings
		$di->set('site.private', function() use ($di) {
			$site = $di->get('site.config');
			$features = $di->get('features');
			return $config['private']
				and $features['private']['enabled'];
		});

		// Intercom config settings
		$di->set('site.intercomAppToken', function() use ($di) {
			return Kohana::$config->load('site.intercomAppToken');
		});

		// Site config
		$di->set('site.config', function() use ($di) {
			return $di->get('repository.config')->get('site')->asArray();
		});

		// Feature config
		$di->set('features', function() use ($di) {
			return $di->get('repository.config')->get('features')->asArray();
		});

		// Roles config settings
		$di->set('roles.enabled', function() use ($di) {
			$config = $di->get('features');

			return $config['roles']['enabled'];
		});

		// Webhooks config settings
		$di->set('webhooks.enabled', function() use ($di) {
			$config = $di->get('features');

			return $config['webhooks']['enabled'];
		});

		// Data import config settings
		$di->set('data-import.enabled', function() use ($di) {
			$config = $di->get('features');

			return $config['data-import']['enabled'];
		});

		$di->set('features.data-providers', function() {
			$config = $di->get('features');

			return array_filter($config['data-providers']);
		});

		// Site config
		$di->set('features.limits', function() use ($di) {
			$config = $di->get('features');

			return $config['limits'];
		});

		$di->set('tool.uploader.prefix', function() use ($di) {
			// Is this a multisite install?
			$multisite = Kohana::$config->load('multisite.enabled');
			if ($multisite) {
				return $di->get('multisite')->getCdnPrefix();
			}

			return '';
		});

		// Multisite utility class
		$di->set('multisite', $di->lazyNew('Ushahidi_Multisite'));
		$di->params['Ushahidi_Multisite'] = [
			'db' => $di->lazyGet('kohana.db.multisite')
		];

		// @todo move into lumen service provider
		$di->set('session.user', function() use ($di) {
			// Using the OAuth resource server, get the userid (owner id) for this request
			// $server = $di->get('oauth.server.resource');
			// $userid = $server->getOwnerId();
			$genericUser = app('auth')->guard()->user();

			// Using the user repository, load the user
			$repo = $di->get('repository.user');
			$user = $repo->get($genericUser ? $genericUser->id : null);

			return $user;
		});

		$di->set('tool.validation', $di->lazyNew('Ushahidi_ValidationEngine'));

		$di->set('tool.mailer', $di->lazyNew('Ushahidi_Mailer', [
			'siteConfig' => $di->lazyGet('site.config')
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
