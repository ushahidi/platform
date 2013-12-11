<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Email Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\Email
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

// Plugin Info
$plugin = array(
	'email' => array(
		'name' => 'Email Data Provider',
		'version' => '0.1',

		// Services Provided By This Plugin
		'services' => array(
			Message_Type::SMS => FALSE,
			Message_Type::IVR => FALSE,
			Message_Type::EMAIL => TRUE,
			Message_Type::TWITTER => FALSE
		),

		// Option Key and Label
		'options' => array(
			'incoming_type' => 'Incoming Server Type',
			'incoming_server' => 'Incoming Server',
			'incoming_port' => 'Incoming Server Port',
			'incoming_security' => 'Incoming Server Security (SSL, TLS, None)',
			'incoming_username' => 'Incoming Username',
			'incoming_password' => 'Incoming Password',
			'outgoing_type' => 'Outgoing Server Type',
			'outgoing_server' => 'Outgoing Server',
			'outgoing_port' => 'Outgoing Server Port',
			'outgoing_security' => 'Outgoing Server Security (SSL, TLS, None)',
			'outgoing_username' => 'Outgoing Username',
			'outgoing_password' => 'Outgoing Password',
			'from_name' => 'Email Sender Name'
		),

		// Links
		'links' => array(
		)
	)
);

// Register the plugin
Event::instance()->fire('Ushahidi_Plugin', array($plugin));

