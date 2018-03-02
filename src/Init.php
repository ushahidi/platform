<?php

/**
 * Ushahidi Platform Bootstrap
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

// @codingStandardsIgnoreFile
// PHPCS doesn't like this file because it declares function AND executes logic

// For dependency management and autoloading, we use [Composer][composer].
//
// **If you haven't already done so, you should run `composer install` now.**
//
// [composer]: http://getcomposer.org/
require __DIR__ . '/../vendor/autoload.php';

// The global [Dependency Injection][di] container lives inside of a global
// `service()` function. This avoids the need to have a global variable, and
// allows for easy access to loading services by using the `$what` parameter.
//
// Currently, we use [Aura.Di][auradi] to power the container.
//
// [di]: https://en.wikipedia.org/wiki/Dependency_injection
// [auradi]: https://github.com/auraphp/Aura.Di/tree/develop-2
function service($what = null)
{
	static $di;
	if (!$di) {
		$di = new Aura\Di\Container(new Aura\Di\Factory);
	}
	if ($what) {
		return $di->get($what);
	}
	return $di;
}

// A special configuration group called "features" stores a list of feature
// toggles. These switches are used to enable and disable specific aspects
// of the platform for varying levels of subscription to Ushahidi-managed
// deployments.

// To make this as easy as possible, we define a global
// feature() function that always responds boolean.
//
// **Features that do not exist will always return `false`.**
function feature($name)
{
	$config = service('repository.config');
	try {
		$conf = $config->get('features');
	} catch (\Exception $e) {
		return false;
	}
	return !empty($conf->$name);
}

// All services set in the container should follow a `prefix.name` format,
// such as `repository.user` or `validate.user.login` or `tool.hash.password`.
//
// When adding services that are private to a plugin, define them with a
// `namespace.`, such as `acme.tool.hash.magic`.
$di = service();

// Disable auto resolution (as recommended in AuraDI docs)
$di->setAutoResolve(false);

// Console application is used for command line tools.
$di->set('app.console', $di->lazyNew('Ushahidi\Console\Application'));

// Any command can be registered with the console app.
$di->params['Ushahidi\Console\Application']['injectCommands'] = [];

// Set up Import command
$di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi\Console\Command\Import');
$di->setter['Ushahidi\Console\Command\Import']['setReaderMap'] = [];
$di->setter['Ushahidi\Console\Command\Import']['setReaderMap']['csv'] = $di->lazyGet('filereader.csv');
$di->setter['Ushahidi\Console\Command\Import']['setTransformer'] = $di->lazyGet('transformer.mapping');
$di->setter['Ushahidi\Console\Command\Import']['setImportUsecase'] = $di->lazy(function () use ($di) {
	return service('factory.usecase')
			->get('posts', 'import')
			// Override authorizer for console
			->setAuthorizer($di->get('authorizer.console'));
});

// User command
$di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi\Console\Command\User');
$di->setter['Ushahidi\Console\Command\User']['setRepo'] = $di->lazyGet('repository.user');
$di->setter['Ushahidi\Console\Command\User']['setTosRepo'] = $di->lazyGet('repository.tos');
$di->setter['Ushahidi\Console\Command\User']['setValidator'] = $di->lazyNew('Ushahidi_Validator_User_Create');

// Config commands
$di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi\Console\Command\ConfigGet');
$di->setter['Ushahidi\Console\Command\ConfigGet']['setUsecase'] = $di->lazy(function () use ($di) {
	return service('factory.usecase')
			->get('config', 'read')
			// Override authorizer for console
			->setAuthorizer($di->get('authorizer.console'))
			// Override formatter for console
			->setFormatter($di->get('formatter.entity.console'));
});
$di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi\Console\Command\ConfigSet');
$di->setter['Ushahidi\Console\Command\ConfigSet']['setUsecase'] = $di->lazy(function () use ($di) {
	return service('factory.usecase')
			->get('config', 'update')
			// Override authorizer for console
			->setAuthorizer($di->get('authorizer.console'))
			// Override formatter for console
			->setFormatter($di->get('formatter.entity.console'));
});

$di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi\Console\Command\ApikeySet');
$di->setter['Ushahidi\Console\Command\ApikeySet']['setUsecase'] = $di->lazy(function () use ($di) {
	return service('factory.usecase')
			->get('apikeys', 'create')
			// Override authorizer for console
			->setAuthorizer($di->get('authorizer.console'))
			// Override formatter for console
			->setFormatter($di->get('formatter.entity.console'));
});

// Validators are used to parse **and** verify input data used for write operations.
$di->set('factory.validator', $di->lazyNew('Ushahidi\Factory\ValidatorFactory'));

// Implemented validators will be mapped to resources and actions.
$di->params['Ushahidi\Factory\ValidatorFactory']['map'] = [];

// Authorizers are used to check if the accessing user has permission to use an action.
$di->set('factory.authorizer', $di->lazyNew('Ushahidi\Factory\AuthorizerFactory'));

// Authorizers are shared, so mapping is done with service names.
$di->params['Ushahidi\Factory\AuthorizerFactory']['map'] = [
	'config'               => $di->lazyGet('authorizer.config'),
	'dataproviders'        => $di->lazyGet('authorizer.dataprovider'),
	'export_jobs'          => $di->lazyGet('authorizer.export_job'),
	'external_auth'        => $di->lazyGet('authorizer.external_auth'),
	'forms'                => $di->lazyGet('authorizer.form'),
	'form_attributes'      => $di->lazyGet('authorizer.form_attribute'),
	'form_roles'           => $di->lazyGet('authorizer.form_role'),
	'form_stages'          => $di->lazyGet('authorizer.form_stage'),
	'tags'                 => $di->lazyGet('authorizer.tag'),
	'layers'               => $di->lazyGet('authorizer.layer'),
	'media'                => $di->lazyGet('authorizer.media'),
	'messages'             => $di->lazyGet('authorizer.message'),
	'posts'                => $di->lazyGet('authorizer.post'),
	'posts_lock'           => $di->lazyGet('authorizer.post_lock'),
	'tags'                 => $di->lazyGet('authorizer.tag'),
	'sets'                 => $di->lazyGet('authorizer.set'),
	'sets_posts'           => $di->lazyGet('authorizer.post'),
	'savedsearches'        => $di->lazyGet('authorizer.savedsearch'),
	'users'                => $di->lazyGet('authorizer.user'),
	'notifications'        => $di->lazyGet('authorizer.notification'),
	'webhooks'             => $di->lazyGet('authorizer.webhook'),
	'apikeys'              => $di->lazyGet('authorizer.apikey'),
	'contacts'             => $di->lazyGet('authorizer.contact'),
	'csv'                  => $di->lazyGet('authorizer.csv'),
	'roles'                => $di->lazyGet('authorizer.role'),
	'permissions'          => $di->lazyGet('authorizer.permission'),
	'posts_export'         => $di->lazyGet('authorizer.post'),
	'tos'				   => $di->lazyGet('authorizer.tos'),
];

// Repositories are used for storage and retrieval of records.
$di->set('factory.repository', $di->lazyNew('Ushahidi\Factory\RepositoryFactory'));

// Repositories are shared, so mapping is done with service names.
$di->params['Ushahidi\Factory\RepositoryFactory']['map'] = [
	'config'               => $di->lazyGet('repository.config'),
	'export_jobs'		   => $di->lazyGet('repository.export_job'),
	'dataproviders'        => $di->lazyGet('repository.dataprovider'),
	'forms'                => $di->lazyGet('repository.form'),
	'form_attributes'      => $di->lazyGet('repository.form_attribute'),
	'form_roles'           => $di->lazyGet('repository.form_role'),
	'form_stages'          => $di->lazyGet('repository.form_stage'),
	'layers'               => $di->lazyGet('repository.layer'),
	'media'                => $di->lazyGet('repository.media'),
	'messages'             => $di->lazyGet('repository.message'),
	'posts'                => $di->lazyGet('repository.post'),
	'posts_lock'           => $di->lazyGet('repository.post_lock'),
	'tags'                 => $di->lazyGet('repository.tag'),
	'sets'                 => $di->lazyGet('repository.set'),
	'sets_posts'           => $di->lazyGet('repository.post'),
	'savedsearches'        => $di->lazyGet('repository.savedsearch'),
	'users'                => $di->lazyGet('repository.user'),
	'notifications'        => $di->lazyGet('repository.notification'),
	'webhooks'             => $di->lazyGet('repository.webhook'),
	'apikeys'              => $di->lazyGet('repository.apikey'),
	'contacts'             => $di->lazyGet('repository.contact'),
	'csv'                  => $di->lazyGet('repository.csv'),
	'roles'                => $di->lazyGet('repository.role'),
	'permissions'          => $di->lazyGet('repository.permission'),
	'posts_export'         => $di->lazyGet('repository.posts_export'),
	'tos'				   => $di->lazyGet('repository.tos'),
];

// Formatters are used for to prepare the output of records. Actions that return
// multiple results use collection formatters for recursion.
$di->set('factory.formatter', $di->lazyNew('Ushahidi\Factory\FormatterFactory'));

// Implemented collection formatter will register as the factory.
$di->params['Ushahidi\Factory\FormatterFactory']['factory'] = null;

// Formatters used on collections of records are run recursively. This expectation
// is mapped by actions that return collections.
$di->params['Ushahidi\Factory\FormatterFactory']['collections'] = [
	'search' => true,
	'update_collection' => true
];

// Data transfer objects are used to carry complex search filters between collaborators.
$di->set('factory.data', $di->lazyNew('Ushahidi\Factory\DataFactory'));

// Usecases that perform searches are the most typical usage of data objects.
$di->params['Ushahidi\Factory\DataFactory']['actions'] = [
	'search' => $di->lazyNew('Ushahidi\Core\SearchData'),
	'stats'  => $di->lazyNew('Ushahidi\Core\SearchData'),
	'export'  => $di->lazyNew('Ushahidi\Core\SearchData'),
];

// Use cases are used to join multiple collaborators together for a single interaction.
$di->set('factory.usecase', $di->lazyNew('Ushahidi\Factory\UsecaseFactory'));
$di->params['Ushahidi\Factory\UsecaseFactory'] = [
	'authorizers'  => $di->lazyGet('factory.authorizer'),
	'repositories' => $di->lazyGet('factory.repository'),
	'formatters'   => $di->lazyGet('factory.formatter'),
	'validators'   => $di->lazyGet('factory.validator'),
	'data'         => $di->lazyGet('factory.data'),
];

// Each of the actions follows a standard sequence of events and is simply constructed
// with a unique set of collaborators that follow specific interfaces.
$di->params['Ushahidi\Factory\UsecaseFactory']['actions'] = [
	'create' => $di->newFactory('Ushahidi\Core\Usecase\CreateUsecase'),
	'read'   => $di->newFactory('Ushahidi\Core\Usecase\ReadUsecase'),
	'update' => $di->newFactory('Ushahidi\Core\Usecase\UpdateUsecase'),
	'delete' => $di->newFactory('Ushahidi\Core\Usecase\DeleteUsecase'),
	'search' => $di->newFactory('Ushahidi\Core\Usecase\SearchUsecase'),
	'options'=> $di->newFactory('Ushahidi\Core\Usecase\OptionsUsecase'),
];

// It is also possible to overload usecases by setting a specific resource and action.
// The same collaborator mapping will be applied by action as with default use cases.
$di->params['Ushahidi\Factory\UsecaseFactory']['map'] = [];

// Config does not allow ordering or sorting, because of its simple key/value nature.
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['config'] = [
	'search' => $di->newFactory('Ushahidi\Core\Usecase\Config\SearchConfig'),
];

// Form sub-endpoints must verify that the form exists before anything else.
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['form_attributes'] = [
	'create'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\CreateFormAttribute'),
	'read'    => $di->lazyNew('Ushahidi\Core\Usecase\Form\ReadFormAttribute'),
	'update'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\UpdateFormAttribute'),
	'delete'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\DeleteFormAttribute'),
	'search'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\SearchFormAttribute'),
];
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['form_roles'] = [
	'update_collection'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\UpdateFormRole'),
	'search'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\SearchFormRole'),
];
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['form_stages'] = [
	'create'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\CreateFormStage'),
	'read'    => $di->lazyNew('Ushahidi\Core\Usecase\Form\ReadFormStage'),
	'update'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\UpdateFormStage'),
	'delete'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\DeleteFormStage'),
	'search'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\SearchFormStage'),
];

// Media create requires file uploading as part of the payload.
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['media'] = [
	'create' => $di->lazyNew('Ushahidi\Core\Usecase\Media\CreateMedia'),
];
$di->setter['Ushahidi\Core\Usecase\Media\CreateMedia']['setUploader'] = $di->lazyGet('tool.uploader');
$di->setter['Ushahidi\Core\Usecase\Media\CreateMedia']['setFilesystem'] = $di->lazyGet('tool.filesystem');

// CSV requires file upload
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['csv'] = [
	'create' => $di->lazyNew('Ushahidi\Core\Usecase\CSV\CreateCSVUsecase'),
	'read'    => $di->lazyNew('Ushahidi\Core\Usecase\ReadUsecase'),
	'delete' => $di->lazyNew('Ushahidi\Core\Usecase\CSV\DeleteCSVUsecase'),
];

$di->setter['Ushahidi\Core\Usecase\CSV\CreateCSVUsecase']['setUploader'] = $di->lazyGet('tool.uploader');
$di->setter['Ushahidi\Core\Usecase\CSV\CreateCSVUsecase']['setReaderFactory'] = $di->lazyGet('csv.reader_factory');
$di->setter['Ushahidi\Core\Usecase\CSV\DeleteCSVUsecase']['setFilesystem'] = $di->lazyGet('tool.filesystem');

// Message update requires extra validation of message direction+status.
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['messages'] = [
	'create' => $di->lazyNew('Ushahidi\Core\Usecase\Message\CreateMessage'),
	'update' => $di->lazyNew('Ushahidi\Core\Usecase\Message\UpdateMessage'),
	'receive' => $di->newFactory('Ushahidi\Core\Usecase\Message\ReceiveMessage'),
];
// Message receive requires extra repos
$di->setter['Ushahidi\Core\Usecase\Message\ReceiveMessage']['setContactRepository']
	= $di->lazyGet('repository.contact');
$di->setter['Ushahidi\Core\Usecase\Message\ReceiveMessage']['setPostRepository'] = $di->lazyGet('repository.post');
$di->setter['Ushahidi\Core\Usecase\Message\ReceiveMessage']['setContactValidator']
	= $di->lazyGet('validator.contact.receive');

// Add custom usecases for posts
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['posts'] = [
	'create'          => $di->lazyNew('Ushahidi\Core\Usecase\Post\CreatePost'),
	'read'            => $di->lazyNew('Ushahidi\Core\Usecase\Post\ReadPost'),
	'update'          => $di->lazyNew('Ushahidi\Core\Usecase\Post\UpdatePost'),
	'webhook-update'  => $di->lazyNew('Ushahidi\Core\Usecase\Post\WebhookUpdatePost'),
	'delete'          => $di->lazyNew('Ushahidi\Core\Usecase\Post\DeletePost'),
	'search'          => $di->lazyNew('Ushahidi\Core\Usecase\Post\SearchPost'),
	'stats'           => $di->lazyNew('Ushahidi\Core\Usecase\Post\StatsPost'),
	'import'          => $di->lazyNew('Ushahidi\Core\Usecase\ImportUsecase'),
	'export'            => $di->lazyNew('Ushahidi\Core\Usecase\Post\ExportPost'),
];

// Add custom create usecase for notifications
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['notifications'] = [
	'create'  => $di->lazyNew('Ushahidi\Core\Usecase\Notification\CreateNotification')
];

// Add custom create usecase for webhooks
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['webhooks'] = [
	'create'  => $di->lazyNew('Ushahidi\Core\Usecase\Webhook\CreateWebhook')
];

// Add custom create usecase for export jobs
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['export_jobs'] = [
	'create'  => $di->lazyNew('Ushahidi\Core\Usecase\Export\Job\CreateJob'),
	'post-count'  => $di->lazyNew('Ushahidi\Core\Usecase\Export\Job\PostCount')
];

// Add custom create usecase for contacts
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['contacts'] = [
	'create'  => $di->lazyNew('Ushahidi\Core\Usecase\Contact\CreateContact')
];

// Add custom create usecase for terms of service
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['tos'] = [
	'create' => $di->lazyNew('Ushahidi\Core\Usecase\Tos\CreateTos'),
	'search' => $di->lazyNew('Ushahidi\Core\Usecase\Tos\SearchTos'),

];

// Add custom usecases for sets_posts
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['sets_posts'] = [
	'search' => $di->lazyNew('Ushahidi\Core\Usecase\Set\SearchSetPost'),
	'create' => $di->lazyNew('Ushahidi\Core\Usecase\Set\CreateSetPost'),
	'delete' => $di->lazyNew('Ushahidi\Core\Usecase\Set\DeleteSetPost'),
	'read'   => $di->lazyNew('Ushahidi\Core\Usecase\Set\ReadSetPost'),
];

// Add custom useses for post_lock
// Add usecase for posts_lock
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['posts_lock'] = [
	'create' => $di->lazyNew('Ushahidi\Core\Usecase\Post\CreatePostLock'),
	'delete' => $di->lazyNew('Ushahidi\Core\Usecase\Post\DeletePostLock'),
];

$di->setter['Ushahidi\Core\Usecase\Post\PostLockTrait']['setPostRepository'] = $di->lazyGet('repository.post');

// Add custom usecases for sets_posts
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['savedsearches'] = [
    'create' => $di->lazyNew('Ushahidi\Core\Usecase\Set\CreateSet'),
];

// Add custom usecases for sets_posts
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['sets'] = [
    'create' => $di->lazyNew('Ushahidi\Core\Usecase\Set\CreateSet'),
];

// Add usecase for posts_export
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['posts_export'] = [
	'export' => $di->lazyNew('Ushahidi\Core\Usecase\Post\Export'),
];

// Set up traits for SetsPosts Usecases
$di->setter['Ushahidi\Core\Usecase\Set\SetRepositoryTrait']['setSetRepository'] = $di->lazyGet('repository.set');
$di->setter['Ushahidi\Core\Usecase\Set\AuthorizeSet']['setSetAuthorizer'] = $di->lazyGet('authorizer.set');

// User login is a custom read the uses authentication.
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['users'] = [
	'login'    => $di->lazyNew('Ushahidi\Core\Usecase\User\LoginUser'),
	'register' => $di->lazyNew('Ushahidi\Core\Usecase\User\RegisterUser'),
	'getresettoken' => $di->lazyNew('Ushahidi\Core\Usecase\User\GetResetToken'),
	'passwordreset' => $di->lazyNew('Ushahidi\Core\Usecase\User\ResetUserPassword'),
];
$di->setter['Ushahidi\Core\Usecase\User\LoginUser']['setAuthenticator'] = $di->lazyGet('tool.authenticator.password');
$di->setter['Ushahidi\Core\Usecase\User\LoginUser']['setRateLimiter'] = $di->lazyGet('ratelimiter.login');
$di->setter['Ushahidi\Core\Usecase\User\GetResetToken']['setMailer'] = $di->lazyGet('tool.mailer');

// Traits

$di->setter['Ushahidi\Core\Usecase\Form\VerifyFormLoaded']['setFormRepository'] = $di->lazyGet('repository.form');
$di->setter['Ushahidi\Core\Usecase\Form\VerifyStageLoaded']['setStageRepository']
	= $di->lazyGet('repository.form_stage');

$di->setter['Ushahidi\Core\Traits\Event']['setEmitter'] = $di->lazyNew('League\Event\Emitter');
$di->setter['Ushahidi\Core\Traits\PrivateDeployment']['setPrivate'] = $di->lazyGet('site.private');
$di->setter['Ushahidi\Core\Traits\WebhookAccess']['setEnabled'] = $di->lazyGet('webhooks.enabled');
$di->setter['Ushahidi\Core\Traits\PostLockingFeature']['setEnabled'] = $di->lazyGet('post-locking.enabled');
$di->setter['Ushahidi\Core\Traits\RedisFeature']['setEnabled'] = $di->lazyGet('redis.enabled');
$di->setter['Ushahidi\Core\Traits\DataImportAccess']['setEnabled'] = $di->lazyGet('data-import.enabled');

// Set ACL for ACL Trait
$di->setter['Ushahidi\Core\Tool\Permissions\AclTrait']['setAcl'] = $di->lazyGet('tool.acl');

// Tools
$di->set('tool.signer', $di->lazyNew('Ushahidi\Core\Tool\Signer'));
$di->set('tool.verifier', $di->lazyNew('Ushahidi\Core\Tool\Verifier'));
$di->set('tool.uploader', $di->lazyNew('Ushahidi\Core\Tool\Uploader'));
$di->params['Ushahidi\Core\Tool\Uploader'] = [
	'fs' => $di->lazyGet('tool.filesystem'),
	'directory_prefix' => $di->lazyGet('tool.uploader.prefix')
	];

// Authorizers
$di->set('authorizer.config', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\ConfigAuthorizer'));
$di->set('authorizer.dataprovider', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\DataProviderAuthorizer'));
$di->set('authorizer.form', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\FormAuthorizer'));
$di->params['Ushahidi\Core\Tool\Authorizer\FormAuthorizer'] = [
	'form_repo' => $di->lazyGet('repository.form'),
	];
$di->set('authorizer.form_attribute', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\FormAttributeAuthorizer'));
$di->params['Ushahidi\Core\Tool\Authorizer\FormAttributeAuthorizer'] = [
	'stage_repo' => $di->lazyGet('repository.form_stage'),
	'stage_auth' => $di->lazyGet('authorizer.form_stage'),
	];
$di->set('authorizer.form_role', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\FormRoleAuthorizer'));
$di->params['Ushahidi\Core\Tool\Authorizer\FormRoleAuthorizer'] = [
	'form_repo' => $di->lazyGet('repository.form'),
	'form_auth' => $di->lazyGet('authorizer.form'),
	];
$di->set('authorizer.form_stage', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\FormStageAuthorizer'));
$di->params['Ushahidi\Core\Tool\Authorizer\FormStageAuthorizer'] = [
	'form_repo' => $di->lazyGet('repository.form'),
	'form_auth' => $di->lazyGet('authorizer.form'),
	];

$di->set('authorizer.user', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\UserAuthorizer'));
$di->set('authorizer.layer', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\LayerAuthorizer'));
$di->set('authorizer.media', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\MediaAuthorizer'));
$di->set('authorizer.message', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\MessageAuthorizer'));
$di->set('authorizer.tag', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\TagAuthorizer'));
$di->set('authorizer.savedsearch', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\SetAuthorizer'));
$di->set('authorizer.set', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\SetAuthorizer'));
$di->set('authorizer.notification', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\NotificationAuthorizer'));
$di->set('authorizer.webhook', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\WebhookAuthorizer'));
$di->set('authorizer.apikey', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\ApiKeyAuthorizer'));
$di->set('authorizer.contact', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\ContactAuthorizer'));
$di->set('authorizer.csv', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\CSVAuthorizer'));
$di->set('authorizer.role', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\RoleAuthorizer'));
$di->set('authorizer.permission', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\PermissionAuthorizer'));
$di->set('authorizer.post', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\PostAuthorizer'));
$di->set('authorizer.post_lock', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\PostAuthorizer'));
$di->set('authorizer.tos', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\TosAuthorizer'));
$di->set('authorizer.external_auth', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\ExternalAuthorizer'));
$di->set('authorizer.export_job', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\ExportJobAuthorizer'));
$di->params['Ushahidi\Core\Tool\Authorizer\PostAuthorizer'] = [
	'post_repo' => $di->lazyGet('repository.post'),
	'form_repo' => $di->lazyGet('repository.form'),
	];

$di->set('authorizer.console', $di->lazyNew('Ushahidi\Console\Authorizer\ConsoleAuthorizer'));


require __DIR__ . '/App/Init.php';
