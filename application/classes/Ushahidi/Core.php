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
			return Kohana::$config->load('site.private')
				and Kohana::$config->load('features.private.enabled');
		});

		// Intercom config settings
		$di->set('thirdparty.intercomAppToken', function() use ($di) {
			return getenv('INTERCOM_APP_TOKEN');
		});

		// Roles config settings
		$di->set('roles.enabled', function() use ($di) {
			return Kohana::$config->load('features.roles.enabled');
		});

		// Webhooks config settings
		$di->set('webhooks.enabled', function() use ($di) {
			return Kohana::$config->load('features.webhooks.enabled');
		});

		// Post Locking config settings
		$di->set('post-locking.enabled', function() use ($di) {
			return Kohana::$config->load('features.post-locking.enabled');
		});

		// Redis config settings
		$di->set('redis.enabled', function() use ($di) {
			return Kohana::$config->load('features.redis.enabled');
		});

		// Data import config settings
		$di->set('data-import.enabled', function() use ($di) {
			return Kohana::$config->load('features.data-import.enabled');
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

		$di->set('session.user', function() use ($di) {
			// Using the OAuth resource server, get the userid (owner id) for this request
			$server = $di->get('oauth.server.resource');
			$userid = $server->getOwnerId();

			// Using the user repository, load the user
			$repo = $di->get('repository.user');
			$user = $repo->get($userid);

			return $user;
		});

		$di->set('session.client', function() use ($di) {
			// Using the OAuth resource server, get the client id for this request
			$server = $di->get('oauth.server.resource');
			$clientid = $server->getClientId();

			return $clientid;
		});

		// Console commands (oauth is disabled, pending T305)
		$di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi_Console_Oauth_Client');
		$di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi_Console_Dataprovider');
		$di->setter['Ushahidi_Console_Dataprovider']['setRepo'] = $di->lazyGet('repository.dataprovider');

		// Notification Collection command
		$di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi_Console_Notification');
		$di->setter['Ushahidi_Console_Notification']['setDatabase'] = $di->lazyGet('kohana.db');
		$di->setter['Ushahidi_Console_Notification']['setPostRepo'] = $di->lazyGet('repository.post');
		$di->setter['Ushahidi_Console_Notification']['setMessageRepo'] = $di->lazyGet('repository.message');
		$di->setter['Ushahidi_Console_Notification']['setContactRepo'] = $di->lazyGet('repository.contact');
		$di->setter['Ushahidi_Console_Notification']['setNotificationQueueRepo'] = $di->lazyGet('repository.notification.queue');

		// Notification SavedSearch command
		$di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi_Console_SavedSearch');
		$di->setter['Ushahidi_Console_SavedSearch']['setSetRepo'] = $di->lazyGet('repository.savedsearch');
		$di->setter['Ushahidi_Console_SavedSearch']['setPostRepo'] = $di->lazyGet('repository.post');
		$di->setter['Ushahidi_Console_SavedSearch']['setMessageRepo'] = $di->lazyGet('repository.message');
		$di->setter['Ushahidi_Console_SavedSearch']['setContactRepo'] = $di->lazyGet('repository.contact');
		$di->setter['Ushahidi_Console_SavedSearch']['setDataFactory'] = $di->lazyGet('factory.data');

		// Post Exporter
		$di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi_Console_PostExporter');
		$di->setter['Ushahidi_Console_PostExporter']['setPostExportRepo'] = $di->lazyGet('repository.posts_export');
		$di->setter['Ushahidi_Console_PostExporter']['setExportJobRepo'] = $di->lazyGet('repository.export_job');
		$di->setter['Ushahidi_Console_PostExporter']['setDataFactory'] = $di->lazyGet('factory.data');
		$di->setter['Ushahidi_Console_PostExporter']['setFileSystem'] = $di->lazyGet('tool.filesystem');
		$di->setter['Ushahidi_Console_PostExporter']['setDatabase'] = $di->lazyGet('kohana.db');

		// Webhook command
		$di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi_Console_Webhook');
		$di->setter['Ushahidi_Console_Webhook']['setDatabase'] = $di->lazyGet('kohana.db');
		$di->setter['Ushahidi_Console_Webhook']['setPostRepo'] = $di->lazyGet('repository.post');
		$di->setter['Ushahidi_Console_Webhook']['setWebhookRepo'] = $di->lazyGet('repository.webhook');
		$di->setter['Ushahidi_Console_Webhook']['setWebhookJobRepo'] = $di->lazyGet('repository.webhook.job');

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
		$di->setter['League\OAuth2\Server\Grant\Password']['setVerifyCredentialsCallback'] = function($email, $password) {
			$usecase = service('factory.usecase')->get('users', 'login')
				->setIdentifiers(compact('email', 'password'));

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
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['form_roles'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Form_Role_Create'),
			'update_collection' => $di->lazyNew('Ushahidi_Validator_Form_Role_Update'),
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
			'webhook-update' => $di->lazyNew('Ushahidi_Validator_Post_Update'),
			'update' => $di->lazyNew('Ushahidi_Validator_Post_Update'),
			'import' => $di->lazyNew('Ushahidi_Validator_Post_Import'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['export_jobs'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Export_Job_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Export_Job_Update'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['posts_lock'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Post_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Post_Update'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['tags'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Tag_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Tag_Update'),
			'delete' => $di->lazyNew('Ushahidi_Validator_Tag_Delete'),
		];

		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['tos'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Tos_Create'),
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
			'update' => $di->lazyNew('Ushahidi_Validator_Set_Update'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['notifications'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Notification_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Notification_Update'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['apikeys'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_ApiKey_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_ApiKey_Update'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['webhooks'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Webhook_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Webhook_Update'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['contacts'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Contact_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Contact_Update'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['sets_posts'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Set_Post_Create'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['csv'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_CSV_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_CSV_Update'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['csv'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_CSV_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_CSV_Update'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['roles'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Role_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Role_Update'),
		];
		$di->params['Ushahidi\Factory\ValidatorFactory']['map']['permissions'] = [
			'create' => $di->lazyNew('Ushahidi_Validator_Permission_Create'),
			'update' => $di->lazyNew('Ushahidi_Validator_Permission_Update'),
		];

		// Validation Trait
		$di->setter['Ushahidi\Core\Tool\ValidationEngineTrait']['setValidation'] = $di->newFactory('Ushahidi_ValidationEngine');
		$di->params['Ushahidi_ValidationEngine']['array'] = [];

		// Formatter mapping
		$di->params['Ushahidi\Factory\FormatterFactory']['map'] = [
			'config'               => $di->lazyNew('Ushahidi_Formatter_Config'),
			'dataproviders'        => $di->lazyNew('Ushahidi_Formatter_Dataprovider'),
			'export_jobs'		   => $di->lazyNew('Ushahidi_Formatter_Export_Job'),
			'forms'                => $di->lazyNew('Ushahidi_Formatter_Form'),
			'form_attributes'      => $di->lazyNew('Ushahidi_Formatter_Form_Attribute'),
			'form_roles'           => $di->lazyNew('Ushahidi_Formatter_Form_Role'),
			'form_stages'          => $di->lazyNew('Ushahidi_Formatter_Form_Stage'),
			'layers'               => $di->lazyNew('Ushahidi_Formatter_Layer'),
			'media'                => $di->lazyNew('Ushahidi_Formatter_Media'),
			'messages'             => $di->lazyNew('Ushahidi_Formatter_Message'),
			'posts'                => $di->lazyNew('Ushahidi_Formatter_Post'),
			'posts_lock'           => $di->lazyNew('Ushahidi_Formatter_Post_Lock'),
			'tags'                 => $di->lazyNew('Ushahidi_Formatter_Tag'),
			'savedsearches'        => $di->lazyNew('Ushahidi_Formatter_Set'),
			'sets'                 => $di->lazyNew('Ushahidi_Formatter_Set'),
			'sets_posts'           => $di->lazyNew('Ushahidi_Formatter_Post'),
			'savedsearches_posts'  => $di->lazyNew('Ushahidi_Formatter_Post'),
			'users'                => $di->lazyNew('Ushahidi_Formatter_User'),
			'notifications'        => $di->lazyNew('Ushahidi_Formatter_Notification'),
			'webhooks'             => $di->lazyNew('Ushahidi_Formatter_Webhook'),
			'apikeys'              => $di->lazyNew('Ushahidi_Formatter_Apikey'),
			'contacts'             => $di->lazyNew('Ushahidi_Formatter_Contact'),
			'csv'                  => $di->lazyNew('Ushahidi_Formatter_CSV'),
			'roles'                => $di->lazyNew('Ushahidi_Formatter_Role'),
			'permissions'          => $di->lazyNew('Ushahidi_Formatter_Permission'),
			// Formatter for post exports. Defaults to CSV export
			'posts_export'         => $di->lazyNew('Ushahidi_Formatter_Post_CSV'),
			'tos'				   => $di->lazyNew('Ushahidi_Formatter_Tos')
		];

		// Formatter parameters
		foreach ([
			'config',
			'dataprovider',
			'export_job',
			'form',
			'form_attribute',
			'form_role',
			'form_stage',
			'layer',
			'media',
			'message',
			'post',
			'post_lock',
			'tag',
			'user',
			'savedsearch',
			'set_post',
			'notification',
			'webhook',
			'apikey',
			'contact',
			'role',
			'permission',
			'tos',
		] as $name)
		{
			$di->setter['Ushahidi_Formatter_' . Text::ucfirst($name, '_')]['setAuth'] =
				$di->lazyGet("authorizer.$name");
		}

		$di->setter['Ushahidi_Formatter_Set']['setAuth'] = $di->lazyGet("authorizer.set");
		$di->setter['Ushahidi_Formatter_CSV']['setAuth'] = $di->lazyGet("authorizer.csv");

		// Set Formatter factory
		$di->params['Ushahidi\Factory\FormatterFactory']['factory'] = $di->newFactory('Ushahidi_Formatter_Collection');

		$di->set('tool.validation', $di->lazyNew('Ushahidi_ValidationEngine'));
		$di->set('tool.jsontranscode', $di->lazyNew('Ushahidi\Core\Tool\JsonTranscode'));

		// Formatters
		$di->set('formatter.entity.api', $di->lazyNew('Ushahidi_Formatter_API'));
		$di->set('formatter.entity.console', $di->lazyNew('Ushahidi_Formatter_Console'));
		$di->set('formatter.entity.post.value', $di->lazyNew('Ushahidi_Formatter_PostValue'));
		$di->set('formatter.entity.post.lock', $di->lazyNew('Ushahidi_Formatter_Post_Lock'));
		$di->set('formatter.entity.post.geojson', $di->lazyNew('Ushahidi_Formatter_Post_GeoJSON'));
		$di->set('formatter.entity.post.geojsoncollection', $di->lazyNew('Ushahidi_Formatter_Post_GeoJSONCollection'));
		$di->set('formatter.entity.post.stats', $di->lazyNew('Ushahidi_Formatter_Post_Stats'));
		$di->set('formatter.entity.post.csv', $di->lazyNew('Ushahidi_Formatter_Post_CSV'));

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
		$di->set('repository.form_role', $di->lazyNew('Ushahidi_Repository_Form_Role'));
		$di->set('repository.form_stage', $di->lazyNew('Ushahidi_Repository_Form_Stage'));
		$di->set('repository.form_attribute', $di->lazyNew('Ushahidi_Repository_Form_Attribute'));
		$di->set('repository.layer', $di->lazyNew('Ushahidi_Repository_Layer'));
		$di->set('repository.media', $di->lazyNew('Ushahidi_Repository_Media'));
		$di->set('repository.message', $di->lazyNew('Ushahidi_Repository_Message'));
		$di->set('repository.post', $di->lazyNew('Ushahidi_Repository_Post'));
		$di->set('repository.csv_post', $di->lazyNew('Ushahidi_Repository_CSVPost'));
		$di->set('repository.post_lock', $di->lazyNew('Ushahidi_Repository_Post_Lock'));
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
		$di->set('repository.notification', $di->lazyNew('Ushahidi_Repository_Notification'));
		$di->set('repository.webhook', $di->lazyNew('Ushahidi_Repository_Webhook'));
		$di->set('repository.apikey', $di->lazyNew('Ushahidi_Repository_ApiKey'));
		$di->set('repository.csv', $di->lazyNew('Ushahidi_Repository_CSV'));
		$di->set('repository.notification.queue', $di->lazyNew('Ushahidi_Repository_Notification_Queue'));
		$di->set('repository.webhook.job', $di->lazyNew('Ushahidi_Repository_Webhook_Job'));
		$di->set('repository.permission', $di->lazyNew('Ushahidi_Repository_Permission'));
		$di->set('repository.oauth.client', $di->lazyNew('OAuth2_Storage_Client'));
		$di->set('repository.oauth.session', $di->lazyNew('OAuth2_Storage_Session'));
		$di->set('repository.oauth.scope', $di->lazyNew('OAuth2_Storage_Scope'));
		$di->set('repository.posts_export', $di->lazyNew('Ushahidi_Repository_Post_Export'));
		$di->set('repository.tos', $di->lazyNew('Ushahidi_Repository_Tos'));
		$di->set('repository.export_job', $di->lazyNew('Ushahidi_Repository_Export_Job'));

		$di->params['Ushahidi_Repository_Export_Job'] = [
			'post_repo' => $di->lazyGet('repository.post')
		];

		$di->setter['Ushahidi_Repository_User']['setHasher'] = $di->lazyGet('tool.hasher.password');

		// Repository parameters

		// Abstract repository parameters
		$di->params['Ushahidi_Repository'] = [
			'db' => $di->lazyGet('kohana.db'),
			];

		// Set up Json Transcode Repository Trait
		$di->setter['Ushahidi_JsonTranscodeRepository']['setTranscoder'] = $di->lazyGet('tool.jsontranscode');

		// Media repository parameters
		$di->params['Ushahidi_Repository_Media'] = [
			'upload' => $di->lazyGet('tool.uploader'),
			];

		// Form Stage repository parameters
		$di->params['Ushahidi_Repository_Form_Stage'] = [
				'form_repo' => $di->lazyGet('repository.form')
		];

		// Form Attribute repository parameters
		$di->params['Ushahidi_Repository_Form_Attribute'] = [
				'form_stage_repo' => $di->lazyGet('repository.form_stage'),
				'form_repo' => $di->lazyGet('repository.form')
		];

		// Post repository parameters
		$di->params['Ushahidi_Repository_Post'] = [
				'form_attribute_repo' => $di->lazyGet('repository.form_attribute'),
				'form_stage_repo' => $di->lazyGet('repository.form_stage'),
				'form_repo' => $di->lazyGet('repository.form'),
				'post_lock_repo' => $di->lazyGet('repository.post_lock'),
				'contact_repo' => $di->lazyGet('repository.contact'),
				'post_value_factory' => $di->lazyGet('repository.post_value_factory'),
				'bounding_box_factory' => $di->newFactory('Util_BoundingBox')
			];

		// Post repository parameters
		$di->params['Ushahidi_Repository_CSVPost'] = [
			'form_attribute_repo' => $di->lazyGet('repository.form_attribute'),
			'form_stage_repo' => $di->lazyGet('repository.form_stage'),
			'form_repo' => $di->lazyGet('repository.form'),
			'post_lock_repo' => $di->lazyGet('repository.post_lock'),
			'contact_repo' => $di->lazyGet('repository.contact'),
			'post_value_factory' => $di->lazyGet('repository.post_value_factory'),
			'bounding_box_factory' => $di->newFactory('Util_BoundingBox')
		];

		$di->set('repository.post.datetime', $di->lazyNew('Ushahidi_Repository_Post_Datetime'));
		$di->set('repository.post.decimal', $di->lazyNew('Ushahidi_Repository_Post_Decimal'));
		$di->set('repository.post.geometry', $di->lazyNew('Ushahidi_Repository_Post_Geometry'));
		$di->set('repository.post.int', $di->lazyNew('Ushahidi_Repository_Post_Int'));
		$di->set('repository.post.point', $di->lazyNew('Ushahidi_Repository_Post_Point'));
		$di->set('repository.post.relation', $di->lazyNew('Ushahidi_Repository_Post_Relation'));
		$di->set('repository.post.text', $di->lazyNew('Ushahidi_Repository_Post_Text'));
		$di->set('repository.post.description', $di->lazyNew('Ushahidi_Repository_Post_Description'));
		$di->set('repository.post.varchar', $di->lazyNew('Ushahidi_Repository_Post_Varchar'));
		$di->set('repository.post.markdown', $di->lazyNew('Ushahidi_Repository_Post_Markdown'));
		$di->set('repository.post.title', $di->lazyNew('Ushahidi_Repository_Post_Title'));
		$di->set('repository.post.media', $di->lazyNew('Ushahidi_Repository_Post_Media'));
		$di->set('repository.post.tags', $di->lazyNew('Ushahidi_Repository_Post_Tags'));

		$di->params['Ushahidi_Repository_Post_Tags'] = [
				'tag_repo' => $di->lazyGet('repository.tag')
		];

		// The post value repo factory
		$di->set('repository.post_value_factory', $di->lazyNew('Ushahidi_Repository_Post_ValueFactory'));
		$di->params['Ushahidi_Repository_Post_ValueFactory'] = [
				// a map of attribute types to repositories
				'map' => [
					'datetime' => $di->lazyGet('repository.post.datetime'),
					'decimal'  => $di->lazyGet('repository.post.decimal'),
					'geometry' => $di->lazyGet('repository.post.geometry'),
					'int'      => $di->lazyGet('repository.post.int'),
					'point'    => $di->lazyGet('repository.post.point'),
					'relation' => $di->lazyGet('repository.post.relation'),
					'text'     => $di->lazyGet('repository.post.text'),
					'description' => $di->lazyGet('repository.post.description'),
					'varchar'  => $di->lazyGet('repository.post.varchar'),
					'markdown'  => $di->lazyGet('repository.post.markdown'),
					'title'    => $di->lazyGet('repository.post.title'),
					'media'    => $di->lazyGet('repository.post.media'),
					'tags'     => $di->lazyGet('repository.post.tags'),
				],
			];

		$di->params['Ushahidi_Repository_Post_Point'] = [
			'decoder' => $di->lazyNew('Symm\Gisconverter\Decoders\WKT')
			];

		// Validators
		$di->set('validator.user.login', $di->lazyNew('Ushahidi_Validator_User_Login'));
		$di->set('validator.contact.create', $di->lazyNew('Ushahidi_Validator_Contact_Create'));
		$di->set('validator.contact.receive', $di->lazyNew('Ushahidi_Validator_Contact_Receive'));

		$di->params['Ushahidi_Validator_Contact_Update'] = [
			'repo' => $di->lazyGet('repository.user'),
		];

		// Dependencies of validators
		$di->params['Ushahidi_Validator_Post_Create'] = [
			'repo' => $di->lazyGet('repository.post'),
			'attribute_repo' => $di->lazyGet('repository.form_attribute'),
			'stage_repo' => $di->lazyGet('repository.form_stage'),
			'tag_repo' => $di->lazyGet('repository.tag'),
			'user_repo' => $di->lazyGet('repository.user'),
			'form_repo' => $di->lazyGet('repository.form'),
			'post_lock_repo' => $di->lazyGet('repository.post_lock'),
			'role_repo' => $di->lazyGet('repository.role'),
			'post_value_factory' => $di->lazyGet('repository.post_value_factory'),
			'post_value_validator_factory' => $di->lazyGet('validator.post.value_factory'),
			];

		$di->params['Ushahidi_Validator_Form_Update'] = [
			'repo' => $di->lazyGet('repository.form'),
			];

		$di->param['Ushahidi_Validator_Form_Attribute_Update'] = [
			'repo' => $di->lazyGet('repository.form_attribute'),
			'form_stage_repo' => $di->lazyGet('repository.form_stage'),
		];
		$di->params['Ushahidi_Validator_Layer_Update'] = [
			'media_repo' => $di->lazyGet('repository.media'),
		];
		$di->params['Ushahidi_Validator_Message_Update'] = [
			'repo' => $di->lazyGet('repository.message'),
		];
		$di->params['Ushahidi_Validator_Message_Create'] = [
			'repo' => $di->lazyGet('repository.message'),
			'user_repo' => $di->lazyGet('repository.user')
		];

		$di->params['Ushahidi_Validator_Message_Receive'] = [
			'repo' => $di->lazyGet('repository.message'),
		];

		$di->params['Ushahidi_Validator_Set_Update'] = [
			'repo' => $di->lazyGet('repository.user'),
			'role_repo' => $di->lazyGet('repository.role'),
		];
		$di->params['Ushahidi_Validator_Notification_Update'] = [
			'user_repo' => $di->lazyGet('repository.user'),
			'collection_repo' => $di->lazyGet('repository.set'),
			'savedsearch_repo' => $di->lazyGet('repository.savedsearch'),
		];
		$di->params['Ushahidi_Validator_Webhook_Update'] = [
			'user_repo' => $di->lazyGet('repository.user'),
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

		$di->params['Ushahidi_Validator_Tag_Update'] = [
			'repo' => $di->lazyGet('repository.tag'),
			'role_repo' => $di->lazyGet('repository.role'),
		];

		$di->params['Ushahidi_Validator_Post_Lock_Update'] = [
			'post_repo' => $di->lazyGet('repository.post_lock'),
		];

		$di->params['Ushahidi_Validator_Tos_Create'] = [
            'user_repo' => $di->lazyGet('repository.user')
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
		$di->params['Ushahidi_Validator_CSV_Create'] = [
			'form_repo' => $di->lazyGet('repository.form'),
		];
		$di->params['Ushahidi_Validator_CSV_Update'] = [
			'form_repo' => $di->lazyGet('repository.form'),
		];
		$di->params['Ushahidi_Validator_Role_Update'] = [
			'permission_repo' => $di->lazyGet('repository.permission'),
		];

		// Validator Setters
		$di->setter['Ushahidi_Validator_Form_Stage_Update'] = [
			'setFormRepo' => $di->lazyGet('repository.form'),
		];
		$di->setter['Ushahidi_Validator_Form_Role_Update'] = [
			'setFormRepo' => $di->lazyGet('repository.form'),
			'setRoleRepo' => $di->lazyGet('repository.role'),
		];
		$di->setter['Ushahidi_Validator_Media_Create'] = [
			'setMaxBytes' => $di->lazy(function() {
				return \Kohana::$config->load('media.max_upload_bytes');
			}),
		];
		$di->setter['Ushahidi_Validator_CSV_Create'] = [
			// @todo load from config
			'setMaxBytes' => '2048000',
		];


		$di->set('validator.post.datetime', $di->lazyNew('Ushahidi_Validator_Post_Datetime'));
		$di->set('validator.post.decimal', $di->lazyNew('Ushahidi_Validator_Post_Decimal'));
		$di->set('validator.post.geometry', $di->lazyNew('Ushahidi_Validator_Post_Geometry'));
		$di->set('validator.post.int', $di->lazyNew('Ushahidi_Validator_Post_Int'));
		$di->set('validator.post.link', $di->lazyNew('Ushahidi_Validator_Post_Link'));
		$di->set('validator.post.point', $di->lazyNew('Ushahidi_Validator_Post_Point'));
		$di->set('validator.post.relation', $di->lazyNew('Ushahidi_Validator_Post_Relation'));
		$di->set('validator.post.varchar', $di->lazyNew('Ushahidi_Validator_Post_Varchar'));
		$di->set('validator.post.markdown', $di->lazyNew('Ushahidi_Validator_Post_Markdown'));
		$di->set('validator.post.video', $di->lazyNew('Ushahidi_Validator_Post_Video'));
		$di->set('validator.post.title', $di->lazyNew('Ushahidi_Validator_Post_Title'));
		$di->set('validator.post.media', $di->lazyNew('Ushahidi_Validator_Post_Media'));
		$di->params['Ushahidi_Validator_Post_Media'] = [
			'media_repo' => $di->lazyGet('repository.media')
		];
		$di->set('validator.post.tags', $di->lazyNew('Ushahidi_Validator_Post_Tags'));
		$di->params['Ushahidi_Validator_Post_Tags'] = [
			'tags_repo' => $di->lazyGet('repository.tag')
		];


		$di->set('validator.post.value_factory', $di->lazyNew('Ushahidi_Validator_Post_ValueFactory'));
		$di->params['Ushahidi_Validator_Post_ValueFactory'] = [
				// a map of attribute types to validators
				'map' => [
					'datetime' => $di->lazyGet('validator.post.datetime'),
					'decimal'  => $di->lazyGet('validator.post.decimal'),
					'geometry' => $di->lazyGet('validator.post.geometry'),
					'int'      => $di->lazyGet('validator.post.int'),
					'link'     => $di->lazyGet('validator.post.link'),
					'point'    => $di->lazyGet('validator.post.point'),
					'relation' => $di->lazyGet('validator.post.relation'),
					'varchar'  => $di->lazyGet('validator.post.varchar'),
					'markdown' => $di->lazyGet('validator.post.markdown'),
					'title'    => $di->lazyGet('validator.post.title'),
					'media'    => $di->lazyGet('validator.post.media'),
					'video'    => $di->lazyGet('validator.post.video'),
					'tags'     => $di->lazyGet('validator.post.tags'),
				],
			];

		$di->params['Ushahidi_Validator_Post_Relation'] = [
			'repo' => $di->lazyGet('repository.post')
			];

		$di->set('transformer.mapping', $di->lazyNew('Ushahidi_Transformer_MappingTransformer'));
		$di->set('transformer.csv', $di->lazyNew('Ushahidi_Transformer_CSVPostTransformer'));
		// Post repo for mapping transformer
		$di->setter['Ushahidi_Transformer_CSVPostTransformer']['setRepo'] =
			$di->lazyGet('repository.post');

		$di->set('tool.mailer', $di->lazyNew('Ushahidi_Mailer'));

		// Event listener for the Set repo
		$di->setter['Ushahidi_Repository_Set']['setEvent'] = 'PostSetEvent';

		$di->setter['Ushahidi_Repository_Set']['setListener'] =
			$di->lazyNew('Ushahidi_Listener_PostSetListener');

		// NotificationQueue repo for Set listener
		$di->setter['Ushahidi_Listener_PostSetListener']['setRepo'] =
			$di->lazyGet('repository.notification.queue');

		// Event listener for the Post repo
		$di->setter['Ushahidi_Repository_Post']['setEvent'] = 'PostCreateEvent';
		$di->setter['Ushahidi_Repository_Post']['setListener'] =
			$di->lazyNew('Ushahidi_Listener_PostListener');

		// WebhookJob repo for Post listener
		$di->setter['Ushahidi_Listener_PostListener']['setRepo'] =
			$di->lazyGet('repository.webhook.job');

		// Webhook repo for Post listener
		$di->setter['Ushahidi_Listener_PostListener']['setWebhookRepo'] =
			$di->lazyGet('repository.webhook');

		// Add Intercom Listener to Config
		$di->setter['Ushahidi_Repository_Config']['setEvent'] = 'ConfigUpdateEvent';
		$di->setter['Ushahidi_Repository_Config']['setListener'] =
			$di->lazyNew('Ushahidi_Listener_IntercomCompanyListener');

		// Add Intercom Listener to Form
		$di->setter['Ushahidi_Repository_Form']['setEvent'] = 'FormUpdateEvent';
		$di->setter['Ushahidi_Repository_Form']['setListener'] =
			$di->lazyNew('Ushahidi_Listener_IntercomCompanyListener');

		// Add Intercom Listener to User
		$di->setter['Ushahidi_Repository_User']['setEvent'] = 'UserGetAllEvent';
		$di->setter['Ushahidi_Repository_User']['setListener'] =
			$di->lazyNew('Ushahidi_Listener_IntercomAdminListener');

		// Add Lock Listener
		$di->setter['Ushahidi_Repository_Post_Lock']['setEvent'] = 'LockBroken';
		$di->setter['Ushahidi_Repository_Post_Lock']['setListener'] =
			$di->lazyNew('Ushahidi_Listener_Lock');
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
