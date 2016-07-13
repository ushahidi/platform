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
		'smssync' => TRUE,
		'frontlinesms' => TRUE,
		'nexmo' => FALSE,
		'email' => FALSE,
	),
	'nexmo' => array(
		'from' => '',
		'api_key' => '',
		'api_secret' => ''
	),
	'smssync' => array(
		'from' => '12345',
		'secret' => '1234'
	),
	'email' => array(
		'incoming_type' => '',
		'incoming_server' => '',
		'incoming_port' => '',
		'incoming_security' => '',
		'incoming_username' => '',
		'incoming_password' => ''
	),
	'frontlinesms' => array(
		'f' => '12345',
		'key' => '1234',
		'frontlinecloud_api_url' => 'http://frontlinesms.com:8130/api/1/webconnection/11',
	),
);
