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
		$di->set('kohana.media.dir', function() use ($di) {
			return Kohana::$config->load('media.media_upload_dir');
		});

		// ACL
		$di->set('acl', function () {
			return A2::instance();
		});

		$di->set('session.user', function() use ($di) {
			// Using the OAuth resource server, get the userid (owner id) for this request
			$server = $di->get('oauth.server.resource');
			$userid = $server->getOwnerId();

			// Using the user repository, load the user
			$repo = $di->get('repository.user');
			$user = $repo->get($userid);

			return $user;
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

		// Parser mapping
		$di->params['Ushahidi\Factory\ParserFactory']['map']['media'] = [
			'create' => $di->lazyNew('Ushahidi_Parser_Media_Create'),
			'read'   => $di->lazyNew('Ushahidi_Parser_Media_Read'),
			'delete' => $di->lazyNew('Ushahidi_Parser_Media_Delete'),
			'search' => $di->lazyNew('Ushahidi_Parser_Media_Search'),
		];
		$di->params['Ushahidi\Factory\ParserFactory']['map']['layers'] = [
			'create' => $di->lazyNew('Ushahidi_Parser_Layer_Create'),
			'read'   => $di->lazyNew('Ushahidi_Parser_Layer_Read'),
			'update' => $di->lazyNew('Ushahidi_Parser_Layer_Update'),
			'delete' => $di->lazyNew('Ushahidi_Parser_Layer_Read'), /* reusing */
			'search' => $di->lazyNew('Ushahidi_Parser_Layer_Search'),
		];
		$di->params['Ushahidi\Factory\ParserFactory']['map']['tags'] = [
			'create' => $di->lazyNew('Ushahidi_Parser_Tag_Create'),
			'read'   => $di->lazyNew('Ushahidi_Parser_Tag_Read'),
			'update' => $di->lazyNew('Ushahidi_Parser_Tag_Update'),
			'delete' => $di->lazyNew('Ushahidi_Parser_Tag_Delete'),
			'search' => $di->lazyNew('Ushahidi_Parser_Tag_Search'),
		];
		$di->params['Ushahidi\Factory\ParserFactory']['map']['posts'] = [
			'read'   => $di->lazyNew('Ushahidi_Parser_Post_Read'),
			'update' => $di->lazyNew('Ushahidi_Parser_Post_Update'),
			'search' => $di->lazyNew('Ushahidi_Parser_Post_Search'),
			'delete' => $di->lazyNew('Ushahidi_Parser_Post_Read'),
		];

		// Validator mapping
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['media'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Media_Create'),
			'delete' => $di->lazyNew('Ushahidi_Validator_Media_Delete'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['layers'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Layer_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Layer_Update'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['tags'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Tag_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Tag_Update'),
			'delete' => $di->lazyNew('Ushahidi_Validator_Tag_Delete'),
		];

		// Validator parameters
		$di->params['Ushahidi_Validator_Layer_Update'] = [
		 	'repo' => $di->lazyGet('repository.layer'),
		 	'media' => $di->lazyGet('repository.media'),
		];
		$di->params['Ushahidi_Validator_Media_Delete'] = [
			'repo' => $di->lazyGet('repository.media'),
			];

		// Formatter mapping
		$di->params['Ushahidi\Factory\FormatterFactory']['map'] = [
			'media'  => $di->lazyNew('Ushahidi_Formatter_Media'),
			'layers' => $di->lazyNew('Ushahidi_Formatter_Layer'),
			'tags'   => $di->lazyNew('Ushahidi_Formatter_Tag'),
			'posts'  => $di->lazyNew('Ushahidi_Formatter_Post'),
		];

		// Formatter parameters
		$di->params['Ushahidi_Formatter_Media'] = [
			'auth' => $di->lazyGet('authorizer.media'),
		];
		$di->params['Ushahidi_Formatter_Tag'] = [
			'auth' => $di->lazyGet('authorizer.tag'),
		];

		// Collection Formatter factory
		$di->params['Ushahidi\Factory\FormatterFactory']['factory'] = $di->newFactory('Ushahidi_Formatter_Collection');

		// Helpers, tools, etc
		$di->set('tool.hasher.password', $di->lazyNew('Ushahidi_Hasher_Password'));
		$di->set('tool.authenticator.password', $di->lazyNew('Ushahidi_Authenticator_Password'));
		$di->set('tool.filesystem', $di->lazyNew('Ushahidi_Filesystem'));

		// Handle filesystem using local paths for now... lots of other options:
		// https://github.com/thephpleague/flysystem/tree/master/src/Adapter
		$di->params['Ushahidi_Filesystem'] = [
			'adapter' => $di->lazyNew('League\Flysystem\Adapter\Local')
			];
		$di->params['League\Flysystem\Adapter\Local'] = [
			'root' => $di->lazyGet('kohana.media.dir'),
			];

		// Formatters
		$di->set('formatter.entity.api', $di->lazyNew('Ushahidi_Formatter_API'));
		$di->set('formatter.entity.post.value', $di->lazyNew('Ushahidi_Formatter_PostValue'));

		$di->set('formatter.output.json', $di->lazyNew('Ushahidi_Formatter_JSON'));
		$di->set('formatter.output.jsonp', $di->lazyNew('Ushahidi_Formatter_JSONP'));


		// Formatter parameters
		$di->setter['Ushahidi_Formatter_JSONP']['setCallback'] = function() {
			return Request::current()->query('callback');
		};
		$di->params['Ushahidi_Formatter_Post'] = [
			'value_formatter' => $di->lazyGet('formatter.entity.post.value')
		];

		// Repositories
		$di->set('repository.config', $di->lazyNew('Ushahidi_Repository_Config'));
		$di->set('repository.contact', $di->lazyNew('Ushahidi_Repository_Contact'));
		$di->set('repository.form_attribute', $di->lazyNew('Ushahidi_Repository_FormAttribute'));
		$di->set('repository.layer', $di->lazyNew('Ushahidi_Repository_Layer'));
		$di->set('repository.media', $di->lazyNew('Ushahidi_Repository_Media'));
		$di->set('repository.post', $di->lazyNew('Ushahidi_Repository_Post'));
		$di->set('repository.tag', $di->lazyNew('Ushahidi_Repository_Tag'));
		$di->set('repository.user', $di->lazyNew('Ushahidi_Repository_User'));
		$di->set('repository.role', $di->lazyNew('Ushahidi_Repository_Role'));
		$di->set('repository.oauth.client', $di->lazyNew('OAuth2_Storage_Client'));
		$di->set('repository.oauth.session', $di->lazyNew('OAuth2_Storage_Session'));
		$di->set('repository.oauth.scope', $di->lazyNew('OAuth2_Storage_Scope'));

		// Abstract repository parameters
		$di->params['Ushahidi_Repository'] = [
			'db' => $di->lazyGet('kohana.db'),
			];
		$di->params['Ushahidi_Repository_Media'] = [
			'upload' => $di->lazyGet('tool.uploader'),
			];
		$di->params['Ushahidi_Repository_Post'] = [
				'form_attribute_repo' => $di->lazyGet('repository.form_attribute'),
				'post_value_factory' => $di->lazyGet('repository.post_value_factory'),
				'bounding_box_factory' => $di->newFactory('Util_BoundingBox'),
				'tag_repo' => $di->lazyGet('repository.tag')
			];

		$di->set('repository.post.datetime', $di->lazyNew('Ushahidi_Repository_PostDatetime'));
		$di->set('repository.post.decimal', $di->lazyNew('Ushahidi_Repository_PostDecimal'));
		$di->set('repository.post.geometry', $di->lazyNew('Ushahidi_Repository_PostGeometry'));
		$di->set('repository.post.int', $di->lazyNew('Ushahidi_Repository_PostInt'));
		$di->set('repository.post.point', $di->lazyNew('Ushahidi_Repository_PostPoint'));
		$di->set('repository.post.text', $di->lazyNew('Ushahidi_Repository_PostText'));
		$di->set('repository.post.varchar', $di->lazyNew('Ushahidi_Repository_PostVarchar'));

		// The post value repo factory
		$di->set('repository.post_value_factory', $di->lazyNew('Ushahidi_Repository_PostValueFactory'));
		$di->params['Ushahidi_Repository_PostValueFactory'] = [
				// a map of attribute types to repositories
				'map' => [
					'datetime' => $di->lazyGet('repository.post.datetime'),
					'decimal'  => $di->lazyGet('repository.post.decimal'),
					'geometry' => $di->lazyGet('repository.post.geometry'),
					'int'      => $di->lazyGet('repository.post.int'),
					'point'    => $di->lazyGet('repository.post.point'),
					'text'     => $di->lazyGet('repository.post.text'),
					'varchar'  => $di->lazyGet('repository.post.varchar')
				],
			];

		$di->params['Ushahidi_Repository_PostPoint'] = [
			'decoder' => $di->lazyNew('Symm\Gisconverter\Decoders\WKT')
			];

		// Parsers
		$di->set('parser.post.read', $di->lazyNew('Ushahidi_Parser_Post_Read'));
		$di->set('parser.post.search', $di->lazyNew('Ushahidi_Parser_Post_Search'));
		$di->set('parser.post.update', $di->lazyNew('Ushahidi_Parser_Post_Update'));
		$di->set('parser.user.login', $di->lazyNew('Ushahidi_Parser_User_Login'));
		$di->set('parser.user.register', $di->lazyNew('Ushahidi_Parser_User_Register'));

		// Dependencies of parsers
		$di->params['Ushahidi_Parser_User_Register'] = [
			'hasher' => $di->lazyGet('tool.hasher.password'),
			];

		// Validators
		$di->set('validator.post.update', $di->lazyNew('Ushahidi_Validator_Post_Update'));
		$di->set('validator.user.login', $di->lazyNew('Ushahidi_Validator_User_Login'));
		$di->set('validator.user.register', $di->lazyNew('Ushahidi_Validator_User_Register'));

		// Dependencies of validators
		$di->params['Ushahidi_Validator_Post_Update'] = [
			'repo' => $di->lazyGet('repository.post'),
			'attribute_repo' => $di->lazyGet('repository.form_attribute'),
			'tag_repo' => $di->lazyGet('repository.tag'),
			'user_repo' => $di->lazyGet('repository.user'),
			'post_value_factory' => $di->lazyGet('repository.post_value_factory'),
			'post_value_validator_factory' => $di->lazyGet('validator.post.value_factory'),
			];
		$di->params['Ushahidi_Validator_Tag_Update'] = [
			'repo' => $di->lazyGet('repository.tag'),
			'role' => $di->lazyGet('repository.role'),
			];
		$di->params['Ushahidi_Validator_User_Register'] = [
			'repo' => $di->lazyGet('repository.user'),
			];

		$di->set('validator.post.datetime', $di->lazyNew('Ushahidi_Validator_Post_Datetime'));
		$di->set('validator.post.decimal', $di->lazyNew('Ushahidi_Validator_Post_Decimal'));
		$di->set('validator.post.int', $di->lazyNew('Ushahidi_Validator_Post_Int'));
		$di->set('validator.post.link', $di->lazyNew('Ushahidi_Validator_Post_Link'));
		$di->set('validator.post.point', $di->lazyNew('Ushahidi_Validator_Post_Point'));
		$di->set('validator.post.varchar', $di->lazyNew('Ushahidi_Validator_Post_Varchar'));

		$di->set('validator.post.value_factory', $di->lazyNew('Ushahidi_Validator_Post_ValueFactory'));
		$di->params['Ushahidi_Validator_Post_ValueFactory'] = [
				// a map of attribute types to repositories
				'map' => [
					'datetime' => $di->lazyGet('validator.post.datetime'),
					'decimal'  => $di->lazyGet('validator.post.decimal'),
					'int'      => $di->lazyGet('validator.post.int'),
					'link'     => $di->lazyGet('validator.post.link'),
					'point'    => $di->lazyGet('validator.post.point'),
					'varchar'  => $di->lazyGet('validator.post.varchar')
				],
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
