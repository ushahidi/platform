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
	'name' => 'Twilio',
	'version' => '0.1',
	// Services Provided By This Plugin
	'services' => array(
		Message_Type::SMS => TRUE,
		Message_Type::IVR => TRUE,
		Message_Type::EMAIL => FALSE,
		Message_Type::TWITTER => FALSE
	),

	// forms Key and Label
	'options' => array(
		'from' => array(
			'label' => 'Phone Number',
			'input' => 'text',
			'description' => 'The from phone number. A Twilio phone number enabled for the type of message you wish to send. '
		),
		'account_sid' => array(
			'label' => 'Account SID',
			'input' => 'text',
			'description' => 'The unique id of the Account that sent this message.'
		),
		'auth_token' => array(
			'label' => 'Auth Token',
			'input' => 'text',
			'description' => '',
		),
		'sms_auto_response' => array(
			'label' => 'SMS Auto response',
			'input' => 'text',
			'description' => '',
		)
	),

	// Links
	'links' => array(
		'developer' => 'https://www.twilio.com',
		'signup' => 'https://www.twilio.com/try-twilio'
	)
);

// Register the plugin
DataProvider::register_provider('twilio', $plugin);

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