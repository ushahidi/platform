<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * SMSSync Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\SMSSync
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

// Plugin Info
$plugin = array(
	'name' => 'SMSSync',
	'version' => '0.1',
	// Services Provided By This Plugin
	'services' => array(
		Message_Type::SMS => TRUE,
		Message_Type::IVR => FALSE,
		Message_Type::EMAIL => FALSE,
		Message_Type::TWITTER => FALSE
	),

	// form Key and Label
	'options' => array(
		'intro_step1' => array(
			'label' => 'Step 1: Download the "SMSSync" app from the Android Market.',
			'input' => 'read-only-text',
			'description' => '
				Scan this QR Code with your phone to download the app from the Android Market <img src="/media/kohana/imagesimages/smssync.png" />'
		),
		// @todo figure out how to inject link and fix base url
		'intro_step2' => array(
			'label' => 'Step 2: Android App Settings',
			'input' => 'read-only-text',
			'description' => 'Turn on SMSSync and use the following link as the website:'
		),
		'secret' => array(
			'label' => 'Secret',
			'input' => 'text',
			'description' => 'Set a secret so that only authorized SMSSync devices can send/recieve message. You need to configure the same secret in the SMSSync App.'
		)
	),

	// Links
	'links' => array(
		'developer' => 'http://smssync.ushahidi.com/',
		'signup' => 'http://smssync.ushahidi.com/'
	)
);

// Register the plugin
DataProvider::register_provider('smssync', $plugin);

// Additional Routes

/**
 * SMS Callback url
 */
Route::set('smssync_sms_callback_url', 'sms/smssync(/<action>)')
	->defaults(array(
		'directory' => 'Sms',
		'controller' => 'Smssync',
	));

/**
 * Legacy SMSSync Callback url
 */
Route::set('smssync_legacy_callback_url', 'smssync(/<action>)')
	->defaults(array(
		'directory' => 'Sms',
		'controller' => 'Smssync'
	));
