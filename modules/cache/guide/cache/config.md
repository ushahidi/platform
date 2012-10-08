# Kohana Cache configuration

Kohana Cache uses configuration groups to create cache instances. A configuration group can
use any supported driver, with successive groups using multiple instances of the same driver type.

The default cache group is loaded based on the `Cache::$default` setting. It is set to the `file` driver as standard, however this can be changed within the `/application/boostrap.php` file.

     // Change the default cache driver to memcache
     Cache::$default = 'memcache';

     // Load the memcache cache driver using default setting
     $memcache = Cache::instance();

## Group settings

Below are the default cache configuration groups for each supported driver. Add to- or override these settings
within the `application/config/cache.php` file.

Name           | Required | Description
-------------- | -------- | ---------------------------------------------------------------
driver         | __YES__  | (_string_) The driver type to use
default_expire | __NO__   | (_string_) The driver type to use


	'file'  => array
	(
		'driver'             => 'file',
		'cache_dir'          => APPPATH.'cache/.kohana_cache',
		'default_expire'     => 3600,
	),

## Memcache & Memcached-tag settings

Name           | Required | Description
-------------- | -------- | ---------------------------------------------------------------
driver         | __YES__  | (_string_) The driver type to use
servers        | __YES__  | (_array_) Associative array of server details, must include a __host__ key. (see _Memcache server configuration_ below)
compression    | __NO__   | (_boolean_) Use data compression when caching

### Memcache server configuration

Name             | Required | Description
---------------- | -------- | ---------------------------------------------------------------
host             | __YES__  | (_string_) The host of the memcache server, i.e. __localhost__; or __127.0.0.1__; or __memcache.domain.tld__
port             | __NO__   | (_integer_) Point to the port where memcached is listening for connections. Set this parameter to 0 when using UNIX domain sockets.  Default __11211__
persistent       | __NO__   | (_boolean_) Controls the use of a persistent connection. Default __TRUE__
weight           | __NO__   | (_integer_) Number of buckets to create for this server which in turn control its probability of it being selected. The probability is relative to the total weight of all servers. Default __1__
timeout          | __NO__   | (_integer_) Value in seconds which will be used for connecting to the daemon. Think twice before changing the default value of 1 second - you can lose all the advantages of caching if your connection is too slow. Default __1__
retry_interval   | __NO__   | (_integer_) Controls how often a failed server will be retried, the default value is 15 seconds. Setting this parameter to -1 disables automatic retry. Default __15__
status           | __NO__   | (_boolean_) Controls if the server should be flagged as online. Default __TRUE__
failure_callback | __NO__   | (_[callback](http://www.php.net/manual/en/language.pseudo-types.php#language.types.callback)_) Allows the user to specify a callback function to run upon encountering an error. The callback is run before failover is attempted. The function takes two parameters, the hostname and port of the failed server. Default __NULL__

	'memcache' => array
	(
		'driver'             => 'memcache',
		'default_expire'     => 3600,
		'compression'        => FALSE,              // Use Zlib compression 
		                                            (can cause issues with integers)
		'servers'            => array
		(
			'local' => array
			(
				'host'             => 'localhost',  // Memcache Server
				'port'             => 11211,        // Memcache port number
				'persistent'       => FALSE,        // Persistent connection
			),
		),
	),
	'memcachetag' => array
	(
		'driver'             => 'memcachetag',
		'default_expire'     => 3600,
		'compression'        => FALSE,              // Use Zlib compression 
		                                            (can cause issues with integers)
		'servers'            => array
		(
			'local' => array
			(
				'host'             => 'localhost',  // Memcache Server
				'port'             => 11211,        // Memcache port number
				'persistent'       => FALSE,        // Persistent connection
			),
		),
	),

## APC settings

	'apc'      => array
	(
		'driver'             => 'apc',
		'default_expire'     => 3600,
	),

## SQLite settings

	'sqlite'   => array
	(
		'driver'             => 'sqlite',
		'default_expire'     => 3600,
		'database'           => APPPATH.'cache/kohana-cache.sql3',
		'schema'             => 'CREATE TABLE caches(id VARCHAR(127) PRIMARY KEY, 
		                                  tags VARCHAR(255), expiration INTEGER, cache TEXT)',
	),

## File settings

	'file'    => array
	(
		'driver'             => 'file',
		'cache_dir'          => 'cache/.kohana_cache',
		'default_expire'     => 3600,
	)

## Wincache settings

	'wincache' => array
	(
		'driver'             => 'wincache',
		'default_expire'     => 3600,
	),


## Override existing configuration group

The following example demonstrates how to override an existing configuration setting, using the config file in `/application/config/cache.php`.

	<?php defined('SYSPATH') or die('No direct script access.');
	return array
	(
		// Override the default configuration
		'memcache'   => array
		(
			'driver'         => 'memcache',  // Use Memcached as the default driver
			'default_expire' => 8000,        // Overide default expiry
			'servers'        => array
			(
				// Add a new server
				array
				(
					'host'       => 'cache.domain.tld',
					'port'       => 11211,
					'persistent' => FALSE
				)
			),
			'compression'    => FALSE
		)
	);

## Add new configuration group

The following example demonstrates how to add a new configuration setting, using the config file in `/application/config/cache.php`.

	<?php defined('SYSPATH') or die('No direct script access.');
	return array
	(
		// Override the default configuration
		'fastkv'   => array
		(
			'driver'         => 'apc',  // Use Memcached as the default driver
			'default_expire' => 1000,   // Overide default expiry
		)
	);
