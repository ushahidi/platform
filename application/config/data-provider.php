<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Ushahidi Settings
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Config
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
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
		'smssync' => FALSE,
		'email' => FALSE,
		'twilio' => FALSE,
		'nexmo' => FALSE,
		'twitter' => FALSE,
		'frontlinesms' => FALSE
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

	'email' => array(
	// 	'from' => '',
	// 	'from_name' => '',

		'incoming_type' => '',
		'incoming_server' => '',
		'incoming_port' => '',
		'incoming_security' => '',
		'incoming_username' => '',
		'incoming_password' => '',

		'outgoing_type' => 'Native',
		'outgoing_server' => '',
		'outgoing_port' => '',
		'outgoing_security' => '',
		'outgoing_username' => '',
		'outgoing_password' => ''
	)
);
