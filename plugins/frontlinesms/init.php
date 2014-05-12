<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * FrontlineSMS Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\FrontlineSMS
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

// Plugin Info
$plugin = array(
	'frontlinesms' => array(
		'name' => 'FrontlineSMS',
		'version' => '0.1',

		// Services Provided By This Plugin
		'services' => array(
			Message_Type::SMS => TRUE
		),

		// Option Key and Label
		'options' => array(
			'f' => 'Phone Number',
			'm' => 'Message',
			'key' => 'Key',
			'frontlinecloud_api_url' => '' // Eg. http://frontlinesms.com:8130/api/1/webconnection/11
		),

		// Links
		'links' => array(
			'overview' => 'http://www.frontlinesms.com/technologies/frontlinesms-overview/',
			'download' => 'http://www.frontlinesms.com/technologies/download/'
		)
	)
);

// Register the plugin
Event::instance()->fire('Ushahidi_Plugin', array($plugin));

// Additional Routes

/**
 * SMS Callback url
 */
Route::set('frontlinesms_sms_callback_url', 'sms/frontlinesms(/<action>)')
	->defaults(array(
		'directory' => 'Sms',
		'controller' => 'frontlinesms',
	));

/**
 * Legacy FrontlineSMS Callback url
 */
Route::set('frontlinesms_legacy_callback_url', 'frontlinesms(/<action>)')
	->defaults(array(
		'directory' => 'Sms',
		'controller' => 'frontlinesms'
	));