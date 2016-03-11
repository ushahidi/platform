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
	'name' => 'Email',
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
		'intro_text' => array(
			'label' => '',
			'input' => 'read-only-text',
			'description' => 'In order to receive reports by email, please input your email account settings below'
		),
		'incoming_type' => array(
			'label' => 'Incoming Server Type',
			'input' => 'radio',
			'description' => '',
			'options' => array('POP', 'IMAP'),
			'rules' => array('required', 'number')
		),
		'incoming_server' => array(
			'label' => 'Incoming Server',
			'input' => 'text',
			'description' => '',
			'description' => 'Examples: mail.yourwebsite.com, imap.gmail.com, pop.gmail.com',
			'rules' => array('required')
		),
		'incoming_port' => array(
			'label' => 'Incoming Server Port',
			'input' => 'text',
			'description' => 'Common ports: 110 (POP3), 143 (IMAP), 995 (POP3 with SSL), 993 (IMAP with SSL)',
			'rules' => array('required','number')
		),
		'incoming_security' => array(
			'label' => 'Incoming Server Security',
			'input' => 'radio',
			'description' => '',
			'options' => array('None', 'SSL', 'TLS')
		),
		'incoming_username' => array(
			'label' => 'Incoming Username',
			'input' => 'text',
			'description' => '',
			'placeholder' => 'Email account username',
			'rules' => array('required')
		),
		'incoming_password' => array(
			'label' => 'Incoming Password',
			'input' => 'text',
			'description' => '',
			'placeholder' => 'Email account password',
			'rules' => array('required')
		),
		'outgoing_type' => array(
			'label' => 'Outgoing Server Type',
			'input' => 'radio',
			'description' => '',
			'options' => array('SMTP', 'sendmail', 'Native')
		),
		'outgoing_server' => array(
			'label' => 'Outgoing Server',
			'input' => 'text',
			'description' => 'Examples: smtp.yourhost.com, smtp.gmail.com',
			'rules' => array('required')
		),
		'outgoing_port' => array(
			'label' => 'Outgoing Server Port',
			'input' => 'text',
			'description' => 'Common ports: 25 (SMTP default), 465 (SMTP with SSL)',
			'rules' => array('required','number')
		),
		'outgoing_security' => array(
			'label' => 'Outgoing Server Security',
			'input' => 'radio',
			'description' => '',
			'options' => array('None', 'SSL', 'TLS')
		),
		'outgoing_username' => array(
			'label' => 'Outgoing Username',
			'input' => 'text',
			'description' => '',
			'placeholder' => 'Email account username',
			'rules' => array('required')
		),
		'outgoing_password' => array(
			'label' => 'Outgoing Password',
			'input' => 'text',
			'description' => '',
			'placeholder' => 'Email account password',
			'rules' => array('required')
		),
		'from' => array(
			'label' => 'Email Address',
			'input' => 'text',
			'description' => 'This will be used to send outgoing emails',
			'rules' => array('required')
		),
		'from_name' => array(
			'label' => 'Email Sender Name',
			'input' => 'text',
			'description' => 'Appears in the \'from:\' field on outgoing emails',
			'rules' => array('required')
		),
	),

	// Links
	'links' => array(
	)
);

// Register the plugin
DataProvider::register_provider('email', $plugin);

