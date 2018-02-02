<?php

/**
 * CDN Config
 */

return array(

	'baseurl' => getenv('CDN_BASEURL'),

	// The default configuration using a local file system setup
	'type' => getenv('CDN_TYPE') ?: 'local',
	'local' => [
		// Where to upload media files eg. images. Take note of the trailing slash.
		// This should be in the Document root.
		'media_upload_dir' => 'media/uploads' // app_path('media/uploads/'),
	],
	// AWS S3 v3 CDN config example
	// 'type' => 'aws',
	'aws' => [
		'key'         => getenv('CDN_AWS_KEY'),
		'secret'      => getenv('CDN_AWS_SECRET'),
		'region'      => getenv('CDN_AWS_REGION'),
		'version'     => getenv('CDN_AWS_VERSION'),
		'bucket_name' => getenv('CDN_AWS_BUCKET'),
	],


	// Rackspace CDN config example
	// 'type' => 'rackspace',
	'rackspace' => [
		'username'  => getenv('CDN_RS_USERNAME'),
		'apiKey'    => getenv('CDN_RS_APIKEY'),
		'region'    => getenv('CDN_RS_REGION'),
		'container' => getenv('CDN_RS_CONTAINER'),
	],
);
