<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Notifications Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\Notifications
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

// Plugin Info
$plugin = array(
	'name' => 'Notifications',
	'version' => '0.1',
	// Services Provided By This Plugin
	'services' => array(
		Message_Type::SMS => FALSE,
		Message_Type::IVR => FALSE,
		Message_Type::EMAIL => FALSE,
		Message_Type::TWITTER => FALSE
	),

	'options' => [],
	'links'   => [],
);

// Register the plugin
DataProvider::register_provider('notifications', $plugin);
