<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

	'driver'     => 'ORM', // orm/jelly/mango/sprig
	'user_model' => 'User',
	'cost'       => 15,    // Bcrypt Cost - any number between 4 and 31 -> higher = stronger hash. FALSE = use default
	'algorithm'  => PASSWORD_BCRYPT, // password constant defining algorithm, available algorithms: http://php.net/manual/en/password.constants.php

	'cookie'     => array(
		'key'         => 'a1_{name}_autologin',
		'lifetime'    => 1209600, // two weeks
	),

	'columns'   => array(
		'username'    => 'username',
		'password'    => 'password',
		'token'       => 'token',
		'last_login'      => 'last_login',      // (optional)
		'logins'          => 'logins',          // (optional),
		'last_attempt'    => 'last_attempt',    // (optional),
		'failed_attempts' => 'failed_attempts', // (optional)
	),

	/**
	 * [!!] Enable the last_attempt & failed_attempt columns to enable rate limiting
	 *
	 * Brute force password attacks take much more time if you disable login for a 
	 * certain amount of time after a certain number of failed logins
	 *
	 * after $key attempts, wait $value seconds between each next attempt
	 */
	'rate_limits' => array(
		3  => 30,  // after 3 failed attempts, wait 30 seconds between each next attempt
		5  => 60,  // after 5 failed attempts, wait 1 minute between each next attempt
		10 => 300  // after 5 failed attempts, wait 10 minutes between each next attempt
	),

	'session'  => array(
		'type'        => 'native' // native or database
	),

	'prevent_browser_cache' => TRUE // Enable this to have A1 set the cache-control & pragma headers when a user is logged in (prevents user from using back button after logout)
);
