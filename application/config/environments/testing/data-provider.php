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
	'default_provider' => array(
		Message_Type::SMS => 'smssync',
		Message_Type::IVR => FALSE,
		Message_Type::EMAIL => 'email',
		Message_Type::TWITTER => 'twitter'
	),
	'providers' => array(
		'smssync' => TRUE,
	),
	'nexmo' => array(
		'from' => '',
		'secret' => '',
		'api_key' => '',
		'api_secret' => ''
	),
	'smssync' => array(
		'from' => '12345',
		'secret' => '1234'
	),
	'email' => array(
		'enabled' => TRUE,
		'incoming_type' => '',
		'incoming_server' => '',
		'incoming_port' => '',
		'incoming_security' => '',
		'incoming_username' => '',
		'incoming_password' => ''
	)
);
