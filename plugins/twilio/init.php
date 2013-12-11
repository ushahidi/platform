<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Twilio Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\Twilio
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

// Plugin Info
$plugin = array(
	'twilio' => array(
		'name' => 'Twilio',
		'version' => '0.1',

		// Services Provided By This Plugin
		'services' => array(
			Message_Type::SMS => TRUE,
			Message_Type::IVR => TRUE,
			Message_Type::EMAIL => FALSE,
			Message_Type::TWITTER => FALSE
		),

		// Option Key and Label
		'options' => array(
			'from' => 'Phone Number',
			'account_sid' => 'Account SID',
			'auth_token' => 'Auth Token',
			'sms_auto_response' => ''
		),

		// Links
		'links' => array(
			'developer' => 'https://www.twilio.com',
			'signup' => 'https://www.twilio.com/try-twilio'
		)
	)
);

// Register the plugin
Event::instance()->fire('Ushahidi_Plugin', array($plugin));

// Additional Routes

/**
 * SMS Callback url
 */
Route::set('twillio_sms_callback_url', 'sms/twillio(/<action>)')
	->defaults(array(
		'directory' => 'Sms',
		'controller' => 'Twillio',
	));

/**
 * Ivr Callback url
 */
Route::set('twillio_ivr_callback_url', 'ivr/twillio(/<action>)')
	->defaults(array(
		'directory' => 'Ivr',
		'controller' => 'Twillio',
	));