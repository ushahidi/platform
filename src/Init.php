<?php

/**
 * Ushahidi Platform Bootstrap
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

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
// of the platform. Often, features are used to to toggle beta or debugging
// code on and off. To make this as easy as possible, we define a global
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

// Console application is used for command line tools.
$di->set('app.console', $di->lazyNew('Ushahidi\Console\Application'));

// Any command can be registered with the console app.
$di->params['Ushahidi\Console\Application']['injectCommands'] = [];

// Parsers are used to parse request data used for read operations.
$di->set('factory.parser', $di->lazyNew('Ushahidi\Factory\ParserFactory'));

// Implemented parsers will be mapped to resources and actions.
$di->params['Ushahidi\Factory\ParserFactory']['map'] = [];

// Validators are used to parse **and** verify input data used for write operations.
$di->set('factory.validator', $di->lazyNew('Ushahidi\Factory\ValidatorFactory'));

// Implemented validators will be mapped to resources and actions.
$di->params['Ushahidi\Factory\ValidatorFactory']['map'] = [];

// Authorizers are used to check if the accessing user has permission to use an action.
$di->set('factory.authorizer', $di->lazyNew('Ushahidi\Factory\AuthorizerFactory'));

// Authorizers are shared, so mapping is done with service names.
$di->params['Ushahidi\Factory\AuthorizerFactory']['map'] = [
	'config'        => $di->lazyGet('authorizer.config'),
	'dataproviders' => $di->lazyGet('authorizer.dataprovider'),
	'forms'         => $di->lazyGet('authorizer.form'),
	'form_attributes' => $di->lazyGet('authorizer.form_attribute'),
	'form_groups'   => $di->lazyGet('authorizer.form_group'),
	'tags'          => $di->lazyGet('authorizer.tag'),
	'layers'        => $di->lazyGet('authorizer.layer'),
	'media'         => $di->lazyGet('authorizer.media'),
	'messages'      => $di->lazyGet('authorizer.message'),
	'posts'         => $di->lazyGet('authorizer.post'),
	'tags'          => $di->lazyGet('authorizer.tag'),
	'sets'          => $di->lazyGet('authorizer.set'),
	'users'         => $di->lazyGet('authorizer.user'),
];

// Repositories are used for storage and retrieval of records.
$di->set('factory.repository', $di->lazyNew('Ushahidi\Factory\RepositoryFactory'));

// Repositories are shared, so mapping is done with service names.
$di->params['Ushahidi\Factory\RepositoryFactory']['map'] = [
	'config'        => $di->lazyGet('repository.config'),
	'dataproviders' => $di->lazyGet('repository.dataprovider'),
	'forms'         => $di->lazyGet('repository.form'),
	'form_attributes' => $di->lazyGet('repository.form_attribute'),
	'form_groups'   => $di->lazyGet('repository.form_group'),
	'layers'        => $di->lazyGet('repository.layer'),
	'media'         => $di->lazyGet('repository.media'),
	'messages'      => $di->lazyGet('repository.message'),
	'posts'         => $di->lazyGet('repository.post'),
	'tags'          => $di->lazyGet('repository.tag'),
	'sets'          => $di->lazyGet('repository.set'),
	'users'			=> $di->lazyGet('repository.user'),
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
];

// Use cases are used to join multiple collaborators together for a single interaction.
$di->set('factory.usecase', $di->lazyNew('Ushahidi\Factory\UsecaseFactory'));
$di->params['Ushahidi\Api\Factory\UsecaseFactory'] = [
	'authorizers'  => $di->lazyGet('factory.authorizer'),
	'parsers'      => $di->lazyGet('factory.parser'),
	'validators'   => $di->lazyGet('factory.validator'),
	'repositories' => $di->lazyGet('factory.repository'),
];

// Each of the actions follows a standard sequence of events and is simply constructed
// with a unique set of collaborators that follow specific interfaces.
$di->params['Ushahidi\Factory\UsecaseFactory']['actions'] = [
	'create' => $di->newFactory('Ushahidi\Core\Usecase\CreateUsecase'),
	'read'   => $di->newFactory('Ushahidi\Core\Usecase\ReadUsecase'),
	'update' => $di->newFactory('Ushahidi\Core\Usecase\UpdateUsecase'),
	'delete' => $di->newFactory('Ushahidi\Core\Usecase\DeleteUsecase'),
	'search' => $di->newFactory('Ushahidi\Core\Usecase\SearchUsecase'),
];

// It is also possible to overload usecases by setting a specific resource and action.
// The same collaborator mapping will be applied by action as with default use cases.
$di->params['Ushahidi\Factory\UsecaseFactory']['map'] = [];

// Config does not allow ordering or sorting, because of its simple key/value nature.
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['config'] = [
	'search' => $di->newFactory('Ushahidi\Core\Usecase\Config\SearchConfig'),
];

// Form sub-endpoints must verify that the form exists before anything else.
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['form_groups'] = [
	'create'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\CreateFormGroup'),
	'read'    => $di->lazyNew('Ushahidi\Core\Usecase\Form\ReadFormGroup'),
	'update'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\UpdateFormGroup'),
	'delete'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\DeleteFormGroup'),
	'search'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\SearchFormGroup'),
];

// Message update requires extra validation of message direction+status.
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['messages'] = [
	'update' => $di->lazyNew('Ushahidi\Core\Usecase\Message\UpdateMessage'),
];

// Add custom usecases for posts
$di->params['Ushahidi\Factory\UsecaseFactory']['map']['posts'] = [
	'create'  => $di->lazyNew('Ushahidi\Core\Usecase\Post\CreatePost'),
	'read'    => $di->lazyNew('Ushahidi\Core\Usecase\Post\ReadPost'),
	'update'  => $di->lazyNew('Ushahidi\Core\Usecase\Post\UpdatePost'),
	'delete'  => $di->lazyNew('Ushahidi\Core\Usecase\Post\DeletePost'),
];

// Usecases also have slightly different interaction styles if they read, write,
// or both. Additional actions should be defined here based on their style.
$di->params['Ushahidi\Factory\UsecaseFactory']['read'] = [
	'read'   => true,
	'update' => true, // + write
	'delete' => true,
	'search' => true,
];
$di->params['Ushahidi\Factory\UsecaseFactory']['write'] = [
	'create' => true,
	'update' => true, // + read
];

// Endpoints are used to cross the boundary between the core application and the
// delivery layer. The endpoint factory is a meta-factory that composes each use
// case when it is required.
$di->set('factory.endpoint', $di->lazyNew('Ushahidi\Api\Factory\EndpointFactory'));
$di->params['Ushahidi\Api\Factory\EndpointFactory'] = [
	'parsers'      => $di->lazyGet('factory.parser'),
	'usecases'     => $di->lazyGet('factory.usecase'),
	'authorizers'  => $di->lazyGet('factory.authorizer'),
	'repositories' => $di->lazyGet('factory.repository'),
	'formatters'   => $di->lazyGet('factory.formatter'),
	// Parsing and formatting happen outside the usecase, in the Endpoint wrapper.
	'factory'      => $di->newFactory('Ushahidi\Api\Endpoint'),
];

// Primary definition of the entire application architecture is here.
// This maps out what services are used for which endpoint, through a very
// strict convention. All services are dependency injected, to allow for
// additional modification and extension.
//
// Each endpoint is defined as `'resource' => [/* list of actions */]`.
// Using `[]` for actions will default to:
//
//     [
//       'create' => true,
//       'read'   => true,
//       'update' => true,
//       'delete' => true,
//       'search' => true,
//     ]
//
// Whatever actions are defined here will be merged with the defaults. This allows
// disabling one or more actions very simple:
//
//     ['search' => false] /* will disable search */
//
// Or if you want to add a new custom action:
//
//     ['special' => true] /* adds "special" action */
//
$di->params['Ushahidi\Api\Factory\EndpointFactory']['endpoints'] = [
	// config cannot be deleted or created, only updated
	'config'          => ['delete' => false, 'post'   => false],
	// data providers cannot be written, only read
	'dataproviders'   => ['create' => false, 'update' => false, 'delete' => false],
	'forms'           => [],
	'form_attributes' => [],
	'form_groups'     => [],
	'layers'          => [],
	// media cannot be updated, only created and deleted
	'media'           => ['update' => false],
	// messages cannot be deleted, only archived (via update)
	'messages'        => ['delete' => false],
	'posts'           => [],
	'sets'            => [],
	'tags'            => [],
	'users'           => [],
];

// Traits
$di->setter['Ushahidi\Core\Traits\UserContext']['setUser'] = $di->lazyGet('session.user');
$di->setter['Ushahidi\Core\Usecase\Form\VerifyFormLoaded']['setFormRepository'] = $di->lazyGet('repository.form');

// Tools
$di->set('tool.uploader', $di->lazyNew('Ushahidi\Core\Tool\Uploader'));
$di->params['Ushahidi\Core\Tool\Uploader'] = [
	'fs' => $di->lazyGet('tool.filesystem'),
	];

// Authorizers
$di->set('authorizer.config', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\ConfigAuthorizer'));
$di->set('authorizer.dataprovider', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\DataProviderAuthorizer'));
$di->set('authorizer.form', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\FormAuthorizer'));
$di->params['Ushahidi\Core\Tool\Authorizer\FormAuthorizer'] = [
	'form_repo' => $di->lazyGet('repository.form'),
	];
$di->set('authorizer.form_attribute', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\FormAttributeAuthorizer'));
$di->set('authorizer.form_group', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\FormGroupAuthorizer'));
$di->params['Ushahidi\Core\Tool\Authorizer\FormGroupAuthorizer'] = [
	'form_repo' => $di->lazyGet('repository.form'),
	'form_auth' => $di->lazyGet('authorizer.form'),
	];

$di->set('authorizer.user', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\UserAuthorizer'));
$di->set('authorizer.layer', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\LayerAuthorizer'));
$di->set('authorizer.media', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\MediaAuthorizer'));
$di->set('authorizer.message', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\MessageAuthorizer'));
$di->set('authorizer.tag', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\TagAuthorizer'));
$di->set('authorizer.set', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\SetAuthorizer'));

$di->set('authorizer.post', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\PostAuthorizer'));
$di->params['Ushahidi\Core\Tool\Authorizer\PostAuthorizer'] = [
	'post_repo' => $di->lazyGet('repository.post'),
	];

// Use cases
$di->set('usecase.user.register', $di->lazyNew('\Ushahidi\Core\Usecase\User\Register'));
$di->params['\Ushahidi\Core\Usecase\User\Register'] = [
	'repo' => $di->lazyGet('repository.user'),
	'valid' => $di->lazyGet('validator.user.register'),
	];

$di->set('usecase.user.login', $di->lazyNew('\Ushahidi\Core\Usecase\User\Login'));
$di->params['\Ushahidi\Core\Usecase\User\Login'] = [
	'repo' => $di->lazyGet('repository.user'),
	'valid' => $di->lazyGet('validator.user.login'),
	'auth' => $di->lazyGet('tool.authenticator.password'),
	];
