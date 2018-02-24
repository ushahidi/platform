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
 * @TODO
 * This should only be for our csv exporter
 */
if (php_sapi_name() !== "cli") {
	$di->setter['Ushahidi\Core\Traits\UserContext']['setUser'] = $di->lazyGet('session.user');
}
/**
 * This service compliments the setUser result when in the cli.
 * The setUser method in UserContext checks in the service if it can't find a user as a fallback
 */
$di->set('usercontext.service', new \Ushahidi\Core\UserContextService());

