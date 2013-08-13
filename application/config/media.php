<?php defined('SYSPATH') or die('No direct script access.');

return array(
	// The public accessible directory where the file will be copied
	'public_dir' => DOCROOT.'media/<uid>/kohana/<filepath>',
	// Write the files to the public directory when in production
	'cache'      => Kohana::$environment === Kohana::PRODUCTION,
	/**
	 * The UID for media files.
	 * This should be unique across the entire project because from a css file
	 * you want to be able to use relative paths to images.
	 * Your css file would not know where an image is if it had a UID of its own.
	 * App versions and repository revisions are good UIDs to use.
	*/
	'uid' => NULL, // Replace this later - needs to get passed into app to change paths there too.
	//'uid' => "3-0-dev",
);
