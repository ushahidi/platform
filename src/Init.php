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
function feature($name) {
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

$di->set('tool.uploader', $di->lazyNew('Ushahidi\Tool\Uploader'));
$di->params['Ushahidi\Tool\Uploader'] = [
	'fs' => $di->lazyGet('tool.filesystem'),
	];

$di->set('usecase.media.create', $di->lazyNew('Ushahidi\Usecase\Media\Create'));
$di->params['Ushahidi\Usecase\Media\Create'] = [
	'repo' => $di->lazyGet('repository.media'),
	'valid' => $di->lazyGet('validator.media.create'),
	// not sure if this goes in the use case or the parser...
	// 'upload' => $di->lazyGet('tool.uploader'),
	];

$di->set('usecase.tag.create', $di->lazyNew('\Ushahidi\Usecase\Tag\Create'));
$di->params['\Ushahidi\Usecase\Tag\Create'] = [
	'repo' => $di->lazyGet('repository.tag'),
	'valid' => $di->lazyGet('validator.tag.create'),
	// 'auth' => $di->lazyGet('tool.authenticator'),
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

