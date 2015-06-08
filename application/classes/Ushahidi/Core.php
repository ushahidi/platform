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

		// Console commands (oauth is disabled, pending T305)
		// $di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi_Console_Oauth_Client');
		// $di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi_Console_Oauth_Token');
		$di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi_Console_Dataprovider');

		$di->setter['Ushahidi_Console_Dataprovider']['setRepo'] = $di->lazyGet('repository.dataprovider');

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
			$usecase = service('factory.usecase')->get('users', 'login')
				->setIdentifiers(compact('username', 'password'));

			try
			{
				$data = $usecase->interact();
				return $data['id'];
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

		// Validator mapping
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['config'] = [
			'update' => $di->lazyNew('Ushahidi_Validator_Config_Update'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['forms'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Form_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Form_Update'),
			'delete' => $di->lazyNew('Ushahidi_Validator_Form_Delete'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['form_attributes'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Form_Attribute_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Form_Attribute_Update'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['form_stages'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Form_Stage_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Form_Stage_Update'),
			'delete' => $di->lazyNew('Ushahidi_Validator_Form_Stage_Delete'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['layers'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Layer_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Layer_Update'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['media'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Media_Create'),
			'delete' => $di->lazyNew('Ushahidi_Validator_Media_Delete'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['posts'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Post_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Post_Create'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['tags'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Tag_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Tag_Update'),
			'delete' => $di->lazyNew('Ushahidi_Validator_Tag_Delete'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['users'] = [
			'create'   => $di->lazyNew('Ushahidi_Validator_User_Create'),
			'update'   => $di->lazyNew('Ushahidi_Validator_User_Update'),
			'register' => $di->lazyNew('Ushahidi_Validator_User_Register')
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['messages'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Message_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Message_Update'),
			'receive' => $di->lazyNew('Ushahidi_Validator_Message_Receive'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['savedsearches'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_SavedSearch_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_SavedSearch_Update'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['sets'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Set_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Set_Create'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['sets_posts'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Set_Post_Create'),
		];


		// Validator Setters
		$di->setter['Ushahidi_Validator_Form_Stage_Update'] = [
			'setFormRepo' => $di->lazyGet('repository.form'),
		];
		$di->setter['Ushahidi_Validator_Media_Create'] = [
			'setMaxBytes' => $di->lazy(function() {
				return \Kohana::$config->load('media.max_upload_bytes');
			}),
		];

		// Validation Trait
		$di->setter['Ushahidi\Core\Tool\ValidationEngineTrait']['setValidation'] = $di->newFactory('Ushahidi_ValidationEngine');

		// Formatter mapping
		$di->params['Ushahidi\Factory\FormatterFactory']['map'] = [
			'config'               => $di->lazyNew('Ushahidi_Formatter_Config'),
			'dataproviders'        => $di->lazyNew('Ushahidi_Formatter_Dataprovider'),
			'forms'                => $di->lazyNew('Ushahidi_Formatter_Form'),
			'form_attributes'      => $di->lazyNew('Ushahidi_Formatter_Form_Attribute'),
			'form_stages'          => $di->lazyNew('Ushahidi_Formatter_Form_Stage'),
			'layers'               => $di->lazyNew('Ushahidi_Formatter_Layer'),
			'media'                => $di->lazyNew('Ushahidi_Formatter_Media'),
			'messages'             => $di->lazyNew('Ushahidi_Formatter_Message'),
			'posts'                => $di->lazyNew('Ushahidi_Formatter_Post'),
			'tags'                 => $di->lazyNew('Ushahidi_Formatter_Tag'),
			'savedsearches'        => $di->lazyNew('Ushahidi_Formatter_Set'),
			'sets'                 => $di->lazyNew('Ushahidi_Formatter_Set'),
			'sets_posts'           => $di->lazyNew('Ushahidi_Formatter_Post'),
			'savedsearches_posts'  => $di->lazyNew('Ushahidi_Formatter_Post'),
			'users'                => $di->lazyNew('Ushahidi_Formatter_User'),
		];

		// Formatter parameters
		foreach ([
			'config',
			'dataprovider',
			'form',
			'form_attribute',
			'form_stage',
			'layer',
			'media',
			'message',
			'post',
			'tag',
			'user',
			'savedsearch',
			'set_post',
		] as $name)
		{
			$di->setter['Ushahidi_Formatter_' . Text::ucfirst($name, '_')]['setAuth'] =
				$di->lazyGet("authorizer.$name");
		}

		$di->setter['Ushahidi_Formatter_Set']['setAuth'] = $di->lazyGet("authorizer.set");

		// Set Formatter factory
		$di->params['Ushahidi\Factory\FormatterFactory']['factory'] = $di->newFactory('Ushahidi_Formatter_Collection');

		// Helpers, tools, etc
		$di->set('tool.hasher.password', $di->lazyNew('Ushahidi_Hasher_Password'));
		$di->set('tool.authenticator.password', $di->lazyNew('Ushahidi_Authenticator_Password'));
		$di->set('tool.filesystem', $di->lazyNew('Ushahidi_Filesystem'));
		$di->set('tool.validation', $di->lazyNew('Ushahidi_ValidationEngine'));
		$di->set('tool.jsontranscode', $di->lazyNew('Ushahidi\Core\Tool\JsonTranscode'));

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
		$di->set('formatter.entity.post.geojson', $di->lazyNew('Ushahidi_Formatter_Post_GeoJSON'));
		$di->set('formatter.entity.post.geojsoncollection', $di->lazyNew('Ushahidi_Formatter_Post_GeoJSONCollection'));
		$di->set('formatter.entity.post.stats', $di->lazyNew('Ushahidi_Formatter_Post_Stats'));

		$di->set('formatter.output.json', $di->lazyNew('Ushahidi_Formatter_JSON'));
		$di->set('formatter.output.jsonp', $di->lazyNew('Ushahidi_Formatter_JSONP'));

		// Formatter parameters
		$di->setter['Ushahidi_Formatter_JSONP']['setCallback'] = function() {
			return Request::current()->query('callback');
		};
		$di->params['Ushahidi_Formatter_Post'] = [
			'value_formatter' => $di->lazyGet('formatter.entity.post.value')
		];
		$di->setter['Ushahidi_Formatter_Post_GeoJSON']['setDecoder'] = $di->lazyNew('Symm\Gisconverter\Decoders\WKT');
		$di->setter['Ushahidi_Formatter_Post_GeoJSONCollection']['setDecoder'] = $di->lazyNew('Symm\Gisconverter\Decoders\WKT');

		// Repositories
		$di->set('repository.config', $di->lazyNew('Ushahidi_Repository_Config'));
		$di->set('repository.contact', $di->lazyNew('Ushahidi_Repository_Contact'));
		$di->set('repository.dataprovider', $di->lazyNew('Ushahidi_Repository_Dataprovider'));
		$di->set('repository.form', $di->lazyNew('Ushahidi_Repository_Form'));
		$di->set('repository.form_stage', $di->lazyNew('Ushahidi_Repository_Form_Stage'));
		$di->set('repository.form_attribute', $di->lazyNew('Ushahidi_Repository_Form_Attribute'));
		$di->set('repository.layer', $di->lazyNew('Ushahidi_Repository_Layer'));
		$di->set('repository.media', $di->lazyNew('Ushahidi_Repository_Media'));
		$di->set('repository.message', $di->lazyNew('Ushahidi_Repository_Message'));
		$di->set('repository.post', $di->lazyNew('Ushahidi_Repository_Post'));
		$di->set('repository.tag', $di->lazyNew('Ushahidi_Repository_Tag'));
		$di->set('repository.set', $di->lazyNew('Ushahidi_Repository_Set'));
		$di->set('repository.savedsearch', $di->lazyNew(
			'Ushahidi_Repository_Set',
			[],
			[
				'setSavedSearch' => true
			]
		));
		$di->set('repository.user', $di->lazyNew('Ushahidi_Repository_User'));
		$di->set('repository.role', $di->lazyNew('Ushahidi_Repository_Role'));
		$di->set('repository.oauth.client', $di->lazyNew('OAuth2_Storage_Client'));
		$di->set('repository.oauth.session', $di->lazyNew('OAuth2_Storage_Session'));
		$di->set('repository.oauth.scope', $di->lazyNew('OAuth2_Storage_Scope'));

		$di->setter['Ushahidi_Repository_User']['setHasher'] = $di->lazyGet('tool.hasher.password');

		// Repository parameters
		foreach ([
			'form_attribute',
			'post',
			'layer',
			'tag',
			'set',
		] as $name)
		{
			$di->setter['Ushahidi_Repository_' . Text::ucfirst($name, '_')]['setTranscoder'] =
				$di->lazyGet('tool.jsontranscode');
		}

		// Abstract repository parameters
		$di->params['Ushahidi_Repository'] = [
			'db' => $di->lazyGet('kohana.db'),
			];
		$di->params['Ushahidi_Repository_Media'] = [
			'upload' => $di->lazyGet('tool.uploader'),
			];
		$di->params['Ushahidi_Repository_Post'] = [
				'form_attribute_repo' => $di->lazyGet('repository.form_attribute'),
				'form_stage_repo' => $di->lazyGet('repository.form_stage'),
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

		// Validators
		$di->set('validator.user.login', $di->lazyNew('Ushahidi_Validator_User_Login'));
		$di->set('validator.contact.create', $di->lazyNew('Ushahidi_Validator_Contact_Create'));
		$di->params['Ushahidi_Validator_Contact_Create'] = [
			'repo' => $di->lazyGet('repository.contact'),
			'user_repo' => $di->lazyGet('repository.user'),
		];

		// Dependencies of validators
		$di->params['Ushahidi_Validator_Post_Create'] = [
			'repo' => $di->lazyGet('repository.post'),
			'attribute_repo' => $di->lazyGet('repository.form_attribute'),
			'stage_repo' => $di->lazyGet('repository.form_stage'),
			'tag_repo' => $di->lazyGet('repository.tag'),
			'user_repo' => $di->lazyGet('repository.user'),
			'form_repo' => $di->lazyGet('repository.form'),
			'role_repo' => $di->lazyGet('repository.role'),
			'post_value_factory' => $di->lazyGet('repository.post_value_factory'),
			'post_value_validator_factory' => $di->lazyGet('validator.post.value_factory'),
			];
		$di->params['Ushahidi_Validator_Tag_Update'] = [
			'repo' => $di->lazyGet('repository.tag'),
			'role_repo' => $di->lazyGet('repository.role'),
			];

		$di->params['Ushahidi_Validator_User_Create'] = [
			'repo' => $di->lazyGet('repository.user'),
			'role_repo' => $di->lazyGet('repository.role'),
			];
		$di->params['Ushahidi_Validator_User_Update'] = [
			'repo' => $di->lazyGet('repository.user'),
			'user' => $di->lazyGet('session.user'),
			'role_repo' => $di->lazyGet('repository.role'),
			];
		$di->params['Ushahidi_Validator_User_Register'] = [
			'repo'    => $di->lazyGet('repository.user')
			];
		$di->params['Ushahidi_Validator_Layer_Update'] = [
			'media_repo' => $di->lazyGet('repository.media'),
		];
		$di->params['Ushahidi_Validator_Message_Update'] = [
			'repo' => $di->lazyGet('repository.message'),
		];
		$di->params['Ushahidi_Validator_Message_Create'] = [
			'repo' => $di->lazyGet('repository.message'),
		];

		$di->params['Ushahidi_Validator_Message_Receive'] = [
			'repo' => $di->lazyGet('repository.message'),
		];

		$di->params['Ushahidi_Validator_Set_Create'] = [
			'repo' => $di->lazyGet('repository.user'),
			'role_repo' => $di->lazyGet('repository.role'),
		];
		$di->params['Ushahidi_Validator_SavedSearch_Create'] = [
			'repo' => $di->lazyGet('repository.user'),
			'role_repo' => $di->lazyGet('repository.role'),
		];
		$di->params['Ushahidi_Validator_SavedSearch_Update'] = [
			'repo' => $di->lazyGet('repository.user'),
			'role_repo' => $di->lazyGet('repository.role'),
		];

		$di->params['Ushahidi_Validator_Set_Post_Create'] = [
			'post_repo' => $di->lazyGet('repository.post')
		];


		$di->set('validator.post.datetime', $di->lazyNew('Ushahidi_Validator_Post_Datetime'));
		$di->set('validator.post.decimal', $di->lazyNew('Ushahidi_Validator_Post_Decimal'));
		$di->set('validator.post.geometry', $di->lazyNew('Ushahidi_Validator_Post_Geometry'));
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
					'geometry' => $di->lazyGet('validator.post.geometry'),
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
