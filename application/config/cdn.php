<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Kohana CDN Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Config
 * @copyright  2015 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

return array(

	'baseurl' => false,

	// The default configuration using a local file system setup
	'type' => 'local',
	'local' => [
		// Where to upload media files eg. images. Take note of the trailing slash.
		// This should be in the Document root.
		'media_upload_dir' => APPPATH.'media'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR,
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
