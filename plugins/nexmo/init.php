<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Nexmo Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\Nexmo
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

// Plugin Info
$plugin = array(
	'nexmo' => array(
		'name' => 'Nexmo',
		'version' => '0.1',

		// Services Provided By This Plugin
		'services' => array(
			Message_Type::SMS => TRUE,
			Message_Type::IVR => FALSE,
			Message_Type::EMAIL => FALSE,
			Message_Type::TWITTER => FALSE
		),

		// Option Key and Label
		'options' => array(
			'from' => 'Phone Number',
			'api_key' => 'API Key',
			'api_secret' => 'API Secret'
		),

		// Links
		'links' => array(
			'developer' => 'https://www.nexmo.com/',
			'signup' => 'https://dashboard.nexmo.com/register'
		)
	)
);

// Register the plugin
Event::instance()->fire('Ushahidi_Plugin', array($plugin));

// Additional Routes
Route::set('nexmo_sms_callback_url', 'sms/nexmo(/<action>)')
	->defaults(array(
		'directory' => 'Sms',
		'controller' => 'Nexmo',
	));
