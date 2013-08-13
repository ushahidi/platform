<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
	'auth'       => MODPATH.'auth',       // Basic authentication
	'cache'      => MODPATH.'cache',      // Caching with multiple backends
	// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	'database'   => MODPATH.'database',   // Database access
	//'image'      => MODPATH.'image',      // Image manipulation
	'orm'        => MODPATH.'orm',        // Object Relationship Mapping
	'unittest'   => MODPATH.'unittest',   // Unit testing
	'minion'     => MODPATH.'minion',
	'migrations' => MODPATH.'migrations',
	'koauth'     => MODPATH.'koauth',
	'media'      => MODPATH.'media',
	'ushahidiui' => MODPATH.'UshahidiUI',
	'ACL'        => MODPATH.'ACL', // Access control layer based on Zend_ACL
	'A1'         => MODPATH.'A1', // Auth library using bcrypt
	'A2'         => MODPATH.'A2', // Tying A1/Auth and ACL together
	// 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
);
