<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Kohana Media Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Config
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

return array(

	// The public accessible directory where the file will be copied
	'public_dir' => DOCROOT.'media/<uid>/<filepath>',
	// Write the files to the public directory when in production
	'cache'      => FALSE, //Kohana::$environment === Kohana::PRODUCTION,
	/**
	 * The UID for media files.
	 * This should be unique across the entire project because from a css file
	 * you want to be able to use relative paths to images.
	 * Your css file would not know where an image is if it had a UID of its own.
	 * App versions and repository revisions are good UIDs to use.
	*/
	'uid' => NULL, // Replace this later - needs to get passed into app to change paths there too.
	// 'uid' => "3-0-dev",

	// Where to upload media files eg. images. Take note of the trailing slash.
	// This should be in the Document root.
	'media_upload_dir' => APPPATH.'media'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR,

	// Width to be used to resize the image to a medium size
	'image_medium_width' => 800,

	// Height to be used to resize the image to a medium size. NULL by default
	// so the image's aspect ratio is maintain when resizing it.
	'image_medium_height' => NULL,

	// Width to be used to resize the image to a thumbnail size
	'image_thumbnail_width' => 70,

	// Height to be used to resize the image to a thumbnail size. NULL by default
	// so the image's aspect ratio is maintain when resizing it.
	'image_thumbnail_height' => NULL,

	// Maximum file upload size in bytes. Remember this figure should not be larger
	// than the maximum file upload size set on the server. 1Mb by default.
	'max_upload_bytes' => getenv('MEDIA_MAX_UPLOAD') ?: '10485760', // 10MB
);
