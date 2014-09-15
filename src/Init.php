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

$di->set('tool.authorizer.layer', $di->lazyNew('Ushahidi\Tool\Authorizer\LayerAuthorizer'));
$di->set('tool.authorizer.tag', $di->lazyNew('Ushahidi\Tool\Authorizer\TagAuthorizer'));
$di->set('tool.authorizer.media', $di->lazyNew('Ushahidi\Tool\Authorizer\MediaAuthorizer'));

// Use cases
$di->set('usecase.media.create', $di->lazyNew('Ushahidi\Usecase\Media\Create'));
$di->params['Ushahidi\Usecase\Media\Create'] = [
	'repo' => $di->lazyGet('repository.media'),
	'valid' => $di->lazyGet('validator.media.create'),
	];

$di->set('usecase.media.delete', $di->lazyNew('Ushahidi\Usecase\Media\Delete'));
$di->params['Ushahidi\Usecase\Media\Delete'] = [
	'repo' => $di->lazyGet('repository.media'),
	'valid' => $di->lazyGet('validator.media.delete'),
	'auth' => $di->lazyGet('tool.authorizer.media'),
	];

$di->set('usecase.tag.create', $di->lazyNew('\Ushahidi\Usecase\Tag\Create'));
$di->params['\Ushahidi\Usecase\Tag\Create'] = [
	'repo' => $di->lazyGet('repository.tag'),
	'valid' => $di->lazyGet('validator.tag.create'),
	// 'auth' => $di->lazyGet('tool.authenticator'),
	];

$di->set('usecase.tag.read', $di->lazyNew('\Ushahidi\Usecase\Tag\Read'));
$di->params['\Ushahidi\Usecase\Tag\Read'] = [
	'repo' => $di->lazyGet('repository.tag'),
	'auth' => $di->lazyGet('tool.authorizer.tag'),
	];

$di->set('usecase.tag.search', $di->lazyNew('\Ushahidi\Usecase\Tag\Search'));
$di->params['\Ushahidi\Usecase\Tag\Search'] = [
	'repo' => $di->lazyGet('repository.tag'),
	'auth' => $di->lazyGet('tool.authorizer.tag'),
	];

$di->set('usecase.tag.update', $di->lazyNew('\Ushahidi\Usecase\Tag\Update'));
$di->params['\Ushahidi\Usecase\Tag\Update'] = [
	'repo' => $di->lazyGet('repository.tag'),
	'valid' => $di->lazyGet('validator.tag.update'),
	];

$di->set('usecase.tag.delete', $di->lazyNew('\Ushahidi\Usecase\Tag\Delete'));
$di->params['\Ushahidi\Usecase\Tag\Delete'] = [
	'repo' => $di->lazyGet('repository.tag'),
	'valid' => $di->lazyGet('validator.tag.delete'),
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

// API Endpoints
$di->set('endpoint.tags.post.collection', $di->lazyNew('UshahidiApi\Endpoint', [
	'parser' => $di->lazyGet('parser.tag.create'),
	'formatter' => $di->lazyGet('formatter.entity.tag'),
	'usecase' => $di->lazyGet('usecase.tag.create'),
]));
$di->set('endpoint.tags.get.collection', $di->lazyNew('UshahidiApi\Endpoint', [
	'parser' => $di->lazyGet('parser.tag.search'),
	'formatter' => $di->lazyGet('formatter.collection.tag'),
	'usecase' => $di->lazyGet('usecase.tag.search'),
]));
$di->set('endpoint.tags.get.index', $di->lazyNew('UshahidiApi\Endpoint', [
	'parser' => $di->lazyGet('parser.tag.read'),
	'formatter' => $di->lazyGet('formatter.entity.tag'),
	'usecase' => $di->lazyGet('usecase.tag.read'),
]));
$di->set('endpoint.tags.put.index', $di->lazyNew('UshahidiApi\Endpoint', [
	'parser' => $di->lazyGet('parser.tag.update'),
	'formatter' => $di->lazyGet('formatter.entity.tag'),
	'usecase' => $di->lazyGet('usecase.tag.update'),
]));
$di->set('endpoint.tags.delete.index', $di->lazyNew('UshahidiApi\Endpoint', [
	'parser' => $di->lazyGet('parser.tag.delete'),
	'formatter' => $di->lazyGet('formatter.entity.tag'),
	'usecase' => $di->lazyGet('usecase.tag.delete'),
]));
