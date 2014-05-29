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
	'default_providers' => array(
		Message_Type::SMS => 'smssync',
		Message_Type::IVR => FALSE,
		Message_Type::EMAIL => 'email',
		Message_Type::TWITTER => 'twitter'
	),
	'providers' => array(
		// List of data providers key=provider value=enabled
		// ie. to enable SMSSync add:
		// 'smssync' => TRUE,
	),

	// Config params for individual providers
	// 'nexmo' => array(
	// 	'from' => '',
	// 	'api_key' => '',
	// 	'api_secret' => ''
	// ),

	// // 'twilio' => array(
	// 	'from' => '',
	// 	'account_sid' => '',
	// 	'auth_token' => '',
	// 	'sms_auto_response' => ''
	// ),

	// 'smssync' => array(
	// 	'from' => '12345',
	// 	'secret' => '1234'
	// ),

	// 'email' => array(
	// 	'from' => '',
	// 	'from_name' => '',

	// 	'incoming_type' => '',
	// 	'incoming_server' => '',
	// 	'incoming_port' => '',
	// 	'incoming_security' => '',
	// 	'incoming_username' => '',
	// 	'incoming_password' => '',

	// 	'outgoing_type' => '',
	// 	'outgoing_server' => '',
	// 	'outgoing_port' => '',
	// 	'outgoing_security' => '',
	// 	'outgoing_username' => '',
	// 	'outgoing_password' => ''
	// )

);