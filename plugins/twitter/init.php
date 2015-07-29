<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Twitter Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\Twitter
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

// Plugin Info
$plugin = array(
	'name' => 'Twitter',
	'version' => '0.1',
	// Services Provided By This Plugin
	'services' => array(
		Message_Type::SMS => FALSE,
		Message_Type::IVR => FALSE,
		Message_Type::EMAIL => FALSE,
		Message_Type::TWITTER => TRUE
	),

	// forms Key and Label
	'options' => array(
		'intro_step1' => array(
			'label' => 'Step 1: Create a new Twitter application',
			'input' => 'read-only-text',
			'description' => function() {
				return 'Create a <a href="https://apps.twitter.com/app/new">new twitter application</a>';
			}
		),
		// @todo figure out how to inject link and fix base url
		'intro_step2' => array(
			'label' => 'Step 2: Generate a consumer key and secret',
			'input' => 'read-only-text',
			'description' => function() {
				return 'Once you\'ve created the application click on "Keys and Access Tokens".<br /> Then click "Generate Consumer Key and Secret".<br /> Copy keys, tokens and secrets into the fields below.';
			}
		),
		'consumer_key' => array(
			'label' => 'Consumer Key',
			'input' => 'text',
			'description' => 'Add the consumer key from your Twitter app. ',
			'rules' => array('required')
		),
		'consumer_secret' => array(
			'label' => 'Consumer Secret',
			'input' => 'text',
			'description' => 'Add the consumer secret from your Twitter app.',
			'rules' => array('required')
		),
		'oauth_access_token' => array(
			'label' => 'Access Token',
			'input' => 'text',
			'description' => 'Add the access token you generated for your Twitter app.',
			'rules' => array('required')
		),
		'oauth_access_token_secret' => array(
			'label' => 'Access Token Secret',
			'input' => 'text',
			'description' => 'Add the access secret that you generated for your Twitter app.',
			'rules' => array('required')
		),
		'twitter_search_terms' => array(
			'label' => 'Twitter search terms',
			'input' => 'text',
			'description' => 'Add search terms separated with commas',
			'rules' => array('required')
		)
	),

	// Links
	'links' => array(
		'developer' => 'https://apps.twitter.com/',
	)
);

// Register the plugin
DataProvider::register_provider('twitter', $plugin);
