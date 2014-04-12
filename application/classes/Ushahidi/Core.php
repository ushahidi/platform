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
		 * 1. Plugin Registration Listener
		 */
		Event::instance()->listen(
			'Ushahidi_Plugin',
			function ($event, $params)
			{
				Ushahidi::register($params);
			}
		);

		/**
		 * 2. Load the plugins
		 */
		self::load();

		/**
		 * Attach database config
		 */
		self::attached_db_config();

		// Set site name in all view
		View::set_global('site_name', Kohana::$config->load('site')->get('site_name'));
	}

	public static function attached_db_config()
	{
		/**
		 * Attach database config to override some settings
		 */
		try
		{
			if (DB::query(Database::SELECT, 'SHOW TABLES LIKE \'config\'')->execute()->count() > 0)
			{
				Kohana::$config->attach(new Ushahidi_Config(array(
					'groups' => array('site', 'test', 'features')
				)));
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
	 * Register A Plugin
	 *
	 * @param array $params
	 */
	public static function register($params)
	{
		if (self::valid_plugin($params))
		{
			$config = Kohana::$config->load('_plugins');
			$config->set(key($params), $params[key($params)]);
		}
	}

	/**
	 * Validate Plugin Parameters
	 *
	 * @param array $params
	 * @return bool valid/invalid
	 */
	public static function valid_plugin($params)
	{
		$path = array_keys($params);
		$path = $path[0];

		if ( ! is_array($params) )
		{
			return FALSE;
		}

		// Validate Name
		if ( ! isset($params[$path]['name']) )
		{
			Kohana::$log->add(Log::ERROR, __("':plugin' does not have 'name'", array(':plugin' => $path)));
			return FALSE;
		}

		// Validate Version
		if ( ! isset($params[$path]['version']) )
		{
			Kohana::$log->add(Log::ERROR, __("':plugin' does not have 'version'", array(':plugin' => $path)));
			return FALSE;
		}

		// Validate Services
		if ( ! isset($params[$path]['services']) OR ! is_array($params[$path]['services']) )
		{
			Kohana::$log->add(Log::ERROR, __("':plugin' does not have 'services' or 'services' is not an array", array(':plugin' => $path)));
			return FALSE;
		}

		// Validate Options
		if ( ! isset($params[$path]['options']) OR ! is_array($params[$path]['options']) )
		{
			Kohana::$log->add(Log::ERROR, __("':plugin' does not have 'options' or 'options' is not an array", array(':plugin' => $path)));
			return FALSE;
		}

		// Validate Links
		if ( ! isset($params[$path]['links']) OR ! is_array($params[$path]['links']) )
		{
			Kohana::$log->add(Log::ERROR, __("':plugin' does not have 'links' or 'links' is not an array", array(':plugin' => $path)));
			return FALSE;
		}

		return TRUE;
	}
}