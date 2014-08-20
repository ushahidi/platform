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
	'name' => 'Nexmo',
	'version' => '0.1',
	// Services Provided By This Plugin
	'services' => array(
		Message_Type::SMS => TRUE,
		Message_Type::IVR => FALSE,
		Message_Type::EMAIL => FALSE,
		Message_Type::TWITTER => FALSE
	),

	// Form Key and Label
	'options' => array(
		'from' => array(
			'label' => 'From',
			'input' => 'text',
			'description' => 'The from number',
			'rules' => array('required')
		),
		'secret' => array(
			'label' => 'Secret',
			'input' => 'text',
			'description' => 'The secret value',
			'rules' => array('required')
		),
		'api_key' => array(
			'label' => 'API Key',
			'input' => 'text',
			'description' => 'The API key',
			'rules' => array('required')
		),
		'api_secret' => array(
			'label' => 'API secret',
			'input' => 'text',
			'description' => 'The API secret',
			'rules' => array('required')
		)
	),

	// Links
	'links' => array(
		'developer' => 'https://www.nexmo.com/',
		'signup' => 'https://dashboard.nexmo.com/register'
	)
);

// Register the plugin
DataProvider::register_provider('nexmo', $plugin);

// Additional Routes
Route::set('nexmo_sms_callback_url', 'sms/nexmo(/<action>)')
	->defaults(array(
		'directory' => 'Sms',
		'controller' => 'Nexmo',
	));
