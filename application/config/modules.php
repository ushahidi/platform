<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Kohana Module Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Config
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

return array(
	// Custom modules
	// 'acme' => MODPATH.'acme',

	// Submodules
	'data-provider' => MODPATH.'data-provider',

	// Vendor modules
	'imagefly'   => VENPATH.'bodom78/kohana-imagefly', // Dynamic image generation
	'media'      => VENPATH.'zeelot/kohana-media',

	// Kohana modules
	'cache'      => MODPATH.'cache',      // Caching with multiple backends
	'image'      => MODPATH.'image',      // Image manipulation
);
