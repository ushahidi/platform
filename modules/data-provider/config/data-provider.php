<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Data Provider Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

return array(
	'default_provider' => 'email',
	'providers' => array(
		// List of data providers key=provider value=enabled
		// ie. to enable SMSSync add:
		// 'smssync' => TRUE,
	),
);