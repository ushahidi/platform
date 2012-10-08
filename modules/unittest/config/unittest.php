<?php defined('SYSPATH') or die('No direct script access.');

return array(

	// If you don't use a whitelist then only files included during the request will be counted
	// If you do, then only whitelisted items will be counted
	'use_whitelist' => TRUE,

	// Items to whitelist, only used in cli
	'whitelist' => array(

		// Should the app be whitelisted?
		// Useful if you just want to test your application
		'app' => TRUE,

		// Set to array(TRUE) to include all modules, or use an array of module names
		// (the keys of the array passed to Kohana::modules() in the bootstrap)
		// Or set to FALSE to exclude all modules
		'modules' => FALSE,

		// If you don't want the Kohana code coverage reports to pollute your app's,
		// then set this to FALSE
		'system' => TRUE,
	),

	// Does what it says on the tin
	// Blacklisted files won't be included in code coverage reports
	// If you use a whitelist then the blacklist will be ignored
	'use_blacklist' => FALSE,

	// List of individual files/folders to blacklist
	'blacklist' => array(
	),
	'test_blacklist' => array(
		'tests/cache',
	)
);
