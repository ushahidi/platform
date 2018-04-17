<?php

// All services set in the container should follow a `prefix.name` format,
// such as `repository.user` or `validate.user.login` or `tool.hash.password`.
//
// When adding services that are private to a plugin, define them with a
// `namespace.`, such as `acme.tool.hash.magic`.
$di = service();

// Helpers, tools, etc
$di->set('tool.acl', $di->lazyNew('Ushahidi\App\Acl'));
$di->setter['Ushahidi\App\Acl']['setRoleRepo'] = $di->lazyGet('repository.role');
$di->setter['Ushahidi\App\Acl']['setRolesEnabled'] = $di->lazyGet('roles.enabled');

$di->set('tool.hasher.password', $di->lazyNew('Ushahidi\App\Hasher\Password'));
$di->set('tool.authenticator.password', $di->lazyNew('Ushahidi\App\Authenticator\Password'));

$di->set('filereader.csv', $di->lazyNew('Ushahidi\App\FileReader\CSV'));
$di->setter['Ushahidi\App\FileReader\CSV']['setReaderFactory'] =
	$di->lazyGet('csv.reader_factory');

$di->set('csv.reader_factory', $di->lazyNew('Ushahidi\App\FileReader\CSVReaderFactory'));

// Register filesystem adapter types
// Currently supported: Local filesysten, AWS S3 v3, Rackspace
// the naming scheme must match the cdn type set in config/cdn
$di->set('adapter.local', $di->lazyNew(
	'Ushahidi\App\FilesystemAdapter\Local',
	['config' => $di->lazyGet('cdn.config')]
));

$di->set('adapter.aws', $di->lazyNew(
	'Ushahidi\App\FilesystemAdapter\AWS',
	['config' => $di->lazyGet('cdn.config')]
));

$di->set('adapter.rackspace', $di->lazyNew(
	'Ushahidi\App\FilesystemAdapter\Rackspace',
	['config' => $di->lazyGet('cdn.config')]
));

// Media Filesystem
// The Ushahidi filesystem adapter returns a flysystem adapter for a given
// cdn type based on the provided configuration
$di->set('tool.filesystem', $di->lazyNew('Ushahidi\App\Filesystem'));
$di->params['Ushahidi\App\Filesystem'] = [
	'adapter' => $di->lazy(function () use ($di) {
			$adapter_type = $di->get('cdn.config');
			$fsa = $di->get('adapter.' . $adapter_type['type']);

			return $fsa->getAdapter();
	})
];

// Defined memcached
$di->set('memcached', $di->lazy(function () use ($di) {
	$config = $di->get('ratelimiter.config');

	$memcached = new Memcached();
	$memcached->addServer($config['memcached']['host'], $config['memcached']['port']);

	return $memcached;
}));

// Set up login rate limiter
$di->set('ratelimiter.login.flap', $di->lazyNew('BehEh\Flaps\Flap'));

$di->params['BehEh\Flaps\Flap'] = [
	'storage' => $di->lazyNew('BehEh\Flaps\Storage\DoctrineCacheAdapter'),
	'name' => 'login'
];

$di->set('ratelimiter.login.strategy', $di->lazyNew('BehEh\Flaps\Throttling\LeakyBucketStrategy'));

// 3 requests every 1 minute by default
$di->params['BehEh\Flaps\Throttling\LeakyBucketStrategy'] = [
	'requests' => 3,
	'timeSpan' => '1m'
];

$di->set('ratelimiter.login', $di->lazyNew('Ushahidi\App\RateLimiter'));

$di->params['Ushahidi\App\RateLimiter'] = [
	'flap' => $di->lazyGet('ratelimiter.login.flap'),
	'throttlingStrategy' => $di->lazyGet('ratelimiter.login.strategy'),
];

$di->params['BehEh\Flaps\Storage\DoctrineCacheAdapter'] = [
	'cache' => $di->lazyGet('ratelimiter.cache')
];

// Rate limit storage cache
$di->set('ratelimiter.cache', function () use ($di) {
	$config = $di->get('ratelimiter.config');
	$cache = $config['cache'];

	if ($cache === 'memcached') {
		$di->setter['Doctrine\Common\Cache\MemcachedCache']['setMemcached'] =
			$di->lazyGet('memcached');

		return $di->newInstance('Doctrine\Common\Cache\MemcachedCache');
	} elseif ($cache === 'filesystem') {
		$di->params['Doctrine\Common\Cache\FilesystemCache'] = [
			'directory' => $config['filesystem']['directory'],
		];

		return $di->newInstance('Doctrine\Common\Cache\FilesystemCache');
	}

	// Fall back to using in-memory cache if none is configured
	return $di->newInstance('Doctrine\Common\Cache\ArrayCache');
});

// Rate limiter violation handler
$di->setter['BehEh\Flaps\Flap']['setViolationHandler'] =
	$di->lazyNew('Ushahidi\App\ThrottlingViolationHandler');


/**
 * This is only be for our csv exporter right now.
 * add to $userContextServiceCommands if you want
 * some commands & URLs to always use the usercontextservice
 * for some scenarios where our oauth user setup is not viable.
 */
// list of commands where we want to add a user not identified by a oauth token
$userContextServiceCommands = ['exporter'];
// list of urls where we want to add a user not identified by a oauth token
$userContextServiceMatches = ['/api/v3/exports/external/'];
// command name if in cli
$commandName = isset($argv[1]) ? $argv[1] : null;
$commandName = php_sapi_name() === 'cli' ? $commandName : null;
/**
 * $setUserContextService true will try to set the regular user. We want it
 * to be false when we find a command that matches
**/
$setUserContextService = array_search($commandName, $userContextServiceCommands) === false;
/**
 * If $setUserContextService is true (because the command din't match)
 * we want to check if this is a url where we need to use the regular user setUser
 * from session.user
 */
if ($setUserContextService) {
	$i =0;
	// get path only.
	$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	// search if any of our listed paths is in the requested url
	while ($setUserContextService == true & $i < count($userContextServiceMatches)) {
		if ($urlMatch = !!strstr($urlPath,$userContextServiceMatches[$i])){
			/**
			 * if we find a path that matches set this to false
			 * to avoid setUser to use the 'session.user' service
			*/
			$setUserContextService = false;
		}
		$i++;
	}
}
/**
 * Finally, we check if $setUserContextService is true, to use session.user
 * UserContextService is still always available, but setUser should not be a user for scenarios
 * where oauth tokens aren't looked up
 */
if ($setUserContextService) {
	$di->setter['Ushahidi\Core\Traits\UserContext']['setUser'] = $di->lazyGet('session.user');
}
/**
 * This service compliments the setUser result when in the cli.
 * The setUser method in UserContext checks in the service if it can't find a user as a fallback
 */
$di->set('usercontext.service', new \Ushahidi\Core\UserContextService());
