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
	'tags'   => $di->lazyGet('authorizer.tag'),
	'media'  => $di->lazyGet('authorizer.media'),
	'layers' => $di->lazyGet('authorizer.layer'),
];

// Repositories are used for storage and retrieval of records.
$di->set('factory.repository', $di->lazyNew('Ushahidi\Factory\RepositoryFactory'));

// Repositories are shared, so mapping is done with service names.
$di->params['Ushahidi\Factory\RepositoryFactory']['map'] = [
	'tags'   => $di->lazyGet('repository.tag'),
	'media'  => $di->lazyGet('repository.media'),
	'layers' => $di->lazyGet('repository.layer'),
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
$di->params['UshahidiApi\Factory\UsecaseFactory'] = [
	'authorizers'  => $di->lazyGet('factory.authorizer'),
	'parsers'      => $di->lazyGet('factory.parser'),
	'validators'   => $di->lazyGet('factory.validator'),
	'repositories' => $di->lazyGet('factory.repository'),
];

// Each of the actions follows a standard sequence of events and is simply constructed
// with a unique set of collaborators that follow specific interfaces.
$di->params['Ushahidi\Factory\UsecaseFactory']['map'] = [
	'create' => $di->newFactory('Ushahidi\Usecase\CreateUsecase'),
	'read'   => $di->newFactory('Ushahidi\Usecase\ReadUsecase'),
	'update' => $di->newFactory('Ushahidi\Usecase\UpdateUsecase'),
	'delete' => $di->newFactory('Ushahidi\Usecase\DeleteUsecase'),
	'search' => $di->newFactory('Ushahidi\Usecase\SearchUsecase'),
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
$di->set('factory.endpoint', $di->lazyNew('UshahidiApi\Factory\EndpointFactory'));
$di->params['UshahidiApi\Factory\EndpointFactory'] = [
	'parsers'      => $di->lazyGet('factory.parser'),
	'usecases'     => $di->lazyGet('factory.usecase'),
	'authorizers'  => $di->lazyGet('factory.authorizer'),
	'repositories' => $di->lazyGet('factory.repository'),
	'formatters'   => $di->lazyGet('factory.formatter'),
];

// Parsing and formatting happen outside the usecase, in the Endpoint wrapper.
$di->params['UshahidiApi\Factory\EndpointFactory']['factory'] = $di->newFactory('UshahidiApi\Endpoint');

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
//     ['special' => true] /* adds "search" action */
//
$di->params['UshahidiApi\Factory\EndpointFactory']['endpoints'] = [
	'tags'   => [],
	'media'  => ['update' => false], // disable update action, media can only be created and deleted
	'layers' => [],
];

// Traits
$di->setter['Ushahidi\Traits\UserContext']['setUser'] = $di->lazyGet('session.user');

// Tools
$di->set('tool.uploader', $di->lazyNew('Ushahidi\Tool\Uploader'));
$di->params['Ushahidi\Tool\Uploader'] = [
	'fs' => $di->lazyGet('tool.filesystem'),
	];

// Authorizers
$di->set('tool.authorizer.config', $di->lazyNew('Ushahidi\Tool\Authorizer\ConfigAuthorizer'));
$di->set('tool.authorizer.post', $di->lazyNew('Ushahidi\Tool\Authorizer\PostAuthorizer'));
$di->params['Ushahidi\Tool\Authorizer\PostAuthorizer'] = [
	'post_repo' => $di->lazyGet('repository.post')
	];

$di->set('authorizer.layer', $di->lazyNew('Ushahidi\Tool\Authorizer\LayerAuthorizer'));
$di->set('authorizer.tag', $di->lazyNew('Ushahidi\Tool\Authorizer\TagAuthorizer'));
$di->set('authorizer.media', $di->lazyNew('Ushahidi\Tool\Authorizer\MediaAuthorizer'));

// Use cases
$di->set('usecase.post.read', $di->lazyNew('\Ushahidi\Usecase\Post\ReadPost'));
$di->params['\Ushahidi\Usecase\Post\ReadPost']['tools'] = $di->lazy(function () use ($di) {
	return [
		'repo' => $di->get('repository.post'),
		'auth' => $di->get('tool.authorizer.post'),
	];
});

$di->set('usecase.post.update', $di->lazyNew('\Ushahidi\Usecase\Post\Update'));
$di->params['\Ushahidi\Usecase\Post\Update'] = [
	'repo' => $di->lazyGet('repository.post'),
	'valid' => $di->lazyGet('validator.post.update'),
	'auth' => $di->lazyGet('tool.authorizer.post'),
	];

$di->set('usecase.user.register', $di->lazyNew('\Ushahidi\Usecase\User\Register'));
$di->params['\Ushahidi\Usecase\User\Register'] = [
	'repo' => $di->lazyGet('repository.user'),
	'valid' => $di->lazyGet('validator.user.register'),
	];

$di->set('usecase.user.login', $di->lazyNew('\Ushahidi\Usecase\User\Login'));
$di->params['\Ushahidi\Usecase\User\Login'] = [
	'repo' => $di->lazyGet('repository.user'),
	'valid' => $di->lazyGet('validator.user.login'),
	'auth' => $di->lazyGet('tool.authenticator.password'),
	];
