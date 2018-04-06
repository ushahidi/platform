<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Test Service Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\FrontlineSMS
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

// Plugin Info
$plugin = array(
	'name' => 'Test Service',
	'version' => '0.1',

	// Services Provided By This Plugin
	'services' => array(
			Message_Type::SMS => TRUE
	),

	'options' => array(
		'api_url' => array(
			'label' => 'API Url',
			'input' => 'text',
			'description' => 'The Test Service API URL',
			'rules' => array('required')
		),
		'key' => array(
				'label' => 'Key',
				'input' => 'text',
				'description' => 'The API key',
				'rules' => array('required')
		),
		'secret' => array(
			'label' => 'Secret',
			'input' => 'text',
			'description' => 'Set a secret so that only authorized Test Service accounts can send/recieve message. You need to configure the same secret in the Test Service.',
			'rules' => array('required')
		)
	),
	'inbound_fields' => array(
		'From' => 'text',
		'To' => 'text',
		'Message' => 'text'
	),

		// Links
	'links' => array(
		'overview' => '',
		'forward' => ''
	)
);

// Register the plugin
DataProvider::register_provider('testservice', $plugin);

// Additional Routes

/**
 * SMS Callback url
 */
Route::set('testservice_sms_callback_url', 'sms/testservice(/<action>)')
	->defaults(array(
		'directory' => 'Sms',
		'controller' => 'testservice',
	));

/**
 * Legacy Test Service Callback url
 */
Route::set('testservice_legacy_callback_url', 'testservice(/<action>)')
	->defaults(array(
		'directory' => 'Sms',
		'controller' => 'testservice'
	));
