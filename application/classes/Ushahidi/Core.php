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
		$di->set('kohana.db', function() use ($di) {
			// todo: is there some way to use different configs here?
			return Database::instance();
		});

		// OAuth servers
		$di->set('oauth.server.auth', function() use ($di) {
			$server = $di->newInstance('League\OAuth2\Server\Authorization');
			$server->addGrantType($di->newInstance('League\OAuth2\Server\Grant\AuthCode'));
			$server->addGrantType($di->newInstance('League\OAuth2\Server\Grant\RefreshToken'));
			$server->addGrantType($di->newInstance('League\OAuth2\Server\Grant\Password'));
			$server->addGrantType($di->newInstance('League\OAuth2\Server\Grant\ClientCredentials'));
			return $server;
		});
		$di->set('oauth.server.resource', $di->lazyNew('League\OAuth2\Server\Resource'));

		// Use Kohana requests for OAuth server requests
		$di->setter['League\OAuth2\Server\Resource']['setRequest'] = $di->lazyNew('OAuth2_Request');
		$di->setter['League\OAuth2\Server\Authorization']['setRequest'] = $di->lazyNew('OAuth2_Request');

		// Custom password authenticator
		$di->setter['League\OAuth2\Server\Grant\Password']['setVerifyCredentialsCallback'] = function($username, $password) {
			$usecase = service('usecase.user.login');
			// todo: parse this? inject it?
			$data    = new Ushahidi\Usecase\User\LoginData(compact('username', 'password'));
			try
			{
				return $usecase->interact($data);
			}
			catch (Exception $e)
			{
				return false;
			}
		};

		// Custom storage interfaces for OAuth servers
		$di->params['League\OAuth2\Server\Authorization'] = [
			'client'  => $di->lazyGet('repository.oauth.client'),
			'session' => $di->lazyGet('repository.oauth.session'),
			'scope'   => $di->lazyGet('repository.oauth.scope'),
			];
		$di->params['League\OAuth2\Server\Resource'] = [
			'session' => $di->lazyNew('OAuth2_Storage_Session'),
			];
		$di->params['OAuth2_Storage'] = [
			'db' => $di->lazyGet('kohana.db'),
			];

		// Helpers, tools, etc
		$di->set('tool.hasher.password', $di->lazyNew('Ushahidi_Hasher_Password'));
		$di->set('tool.authenticator', $di->lazyNew('Ushahidi_Authenticator'));
		$di->set('tool.authenticator.password', $di->lazyNew('Ushahidi_Authenticator_Password'));

		// Formatters
		$di->set('formatter.entity.api', $di->lazyNew('Ushahidi_Formatter_API'));
		$di->set('formatter.output.json', $di->lazyNew('Ushahidi_Formatter_JSON'));
		$di->set('formatter.output.jsonp', $di->lazyNew('Ushahidi_Formatter_JSONP'));

		// Formatter parameters
		$di->setter['Ushahidi_Formatter_JSONP']['setCallback'] = function() {
			return Request::current()->query('callback');
		};

		// Repositories
		$di->set('repository.config', $di->lazyNew('Ushahidi_Repository_Config'));
		$di->set('repository.contact', $di->lazyNew('Ushahidi_Repository_Contact'));
		$di->set('repository.tag', $di->lazyNew('Ushahidi_Repository_Tag'));
		$di->set('repository.user', $di->lazyNew('Ushahidi_Repository_User'));
		$di->set('repository.oauth.client', $di->lazyNew('OAuth2_Storage_Client'));
		$di->set('repository.oauth.session', $di->lazyNew('OAuth2_Storage_Session'));
		$di->set('repository.oauth.scope', $di->lazyNew('OAuth2_Storage_Scope'));

		// Abstract repository parameters
		$di->params['Ushahidi_Repository'] = [
			'db' => $di->lazyGet('kohana.db'),
			];
		$di->params['Ushahidi_Repository_Collection'] = [
			'auth' => $di->lazyGet('tool.authenticator'),
			];

		// User login dependencies
		$di->set('parser.user.login', $di->lazyNew('Ushahidi_Parser_User_Login'));
		$di->set('validator.user.login', $di->lazyNew('Ushahidi_Validator_User_Login'));

		// User registration dependencies
		$di->set('validator.user.register', $di->lazyNew('Ushahidi_Validator_User_Register'));
		$di->params['Ushahidi_Validator_User_Register'] = [
			'repo' => $di->lazyGet('repository.user'),
			];

		$di->set('parser.user.register', $di->lazyNew('Ushahidi_Parser_User_Register'));
		$di->params['Ushahidi_Parser_User_Register'] = [
			'hasher' => $di->lazyGet('tool.hasher.password'),
			];

		// Tag dependendies 
		$di->set('parser.tag.create', $di->lazyNew('Ushahidi_Parser_Tag_Create'));
		$di->set('validator.tag.create', $di->lazyNew('Ushahidi_Validator_Tag_Create'));
		$di->params['Ushahidi_Validator_Tag_Create'] = [
			'repo' => $di->lazyGet('repository.tag'),
			];

		/**
		 * 1. Load the plugins
		 */
		self::load();

		/**
		 * Attach database config
		 */
		self::attached_db_config();

		// Set site name in all view
		View::set_global('site_name', service('repository.config')->get('site')->site_name);
	}

	public static function attached_db_config()
	{
		// allowed groups are stored with the config service.
		$groups = service('repository.config')->groups();

		/**
		 * Attach database config to override some settings
		 */
		try
		{
			if (DB::query(Database::SELECT, 'SHOW TABLES LIKE \'config\'')->execute()->count() > 0)
			{
				Kohana::$config->attach(new Ushahidi_Config(array(
					'groups' => $groups
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

}
