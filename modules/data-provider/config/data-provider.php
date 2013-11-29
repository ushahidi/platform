<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Data Provider Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Config
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

return array(
	'default_provider' => 'email',
	'providers' => array(
		// List of data providers key=provider value=enabled
		// ie. to enable SMSSync add:
		// 'smssync' => TRUE,
	),
);