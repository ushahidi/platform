<?php

/**
 * CDN Config
 */

return array(

	'baseurl' => false,

	// The default configuration using a local file system setup
	'type' => 'local',
	'local' => [
		// Where to upload media files eg. images. Take note of the trailing slash.
		// This should be in the Document root.
		'media_upload_dir' => 'media/uploads' // app_path('media/uploads/'),
	]
	// AWS S3 v3 CDN config example
	/*
	'type' => 'aws',
	'aws' => [
		'key'         => '',
		'secret'      => '',
		'region'      => '',
		'version'     => '',
		'bucket_name' => '',
	]
	*/

	// Rackspace CDN config example
	/*
	'type' => 'rackspace',
		'rackspace' => [
		'username'  => '',
		'apiKey'    => '',
		'region'    => '',
		'container' => ''
	]
	*/
);
