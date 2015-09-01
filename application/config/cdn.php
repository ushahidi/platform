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

	// The default configuration using a local file system setup
  'cdn_type' => 'local',
  // AWS S3 v3 CDN config example
  /*
  'cdn_type' => 'aws',

  'key' => '',
  'secret' => '',
  'region' => '',
  'version' => '',
  'bucket_name' => '',
  */
  // Rackspace CDN config example
  /*
  'cdn_type' => 'rackspace',
  'username' => '',
  'apiKey' => '',
  */
)
