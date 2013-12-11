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
	'smssync' => array(
		'name' => 'SMSSync',
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
			'secret' => 'Secret',
		),

		// Option Key and Label
		'feed_options' => array(
		),

		// Links
		'links' => array(
			'developer' => 'http://smssync.ushahidi.com/',
			'signup' => 'http://smssync.ushahidi.com/'
		)
	)
);

// Register the plugin
Event::instance()->fire('Ushahidi_Plugin', array($plugin));

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
