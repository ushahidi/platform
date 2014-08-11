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

	// Ushahidi modules
	'ushahidiui' => APPPATH.'../web',

	// Submodules
	'data-provider' => MODPATH.'data-provider',

	// Vendor modules
	'ACL'        => VENPATH.'wouter/acl', // Access control layer based on Zend_ACL
	'A1'         => VENPATH.'wouter/a1', // Auth library using bcrypt
	'A2'         => VENPATH.'wouter/a2', // Tying A1/Auth and ACL together
	'email'      => VENPATH.'shadowhand/email',
	'imagefly'   => VENPATH.'bodom78/kohana-imagefly', // Dynamic image generation
	'media'      => VENPATH.'zeelot/kohana-media',

	// Kohana modules
	'cache'      => MODPATH.'cache',      // Caching with multiple backends
	'database'   => MODPATH.'database',   // Database access
	'image'      => MODPATH.'image',      // Image manipulation
	'orm'        => MODPATH.'orm',        // Object Relationship Mapping
	'minion'     => MODPATH.'minion',

);
