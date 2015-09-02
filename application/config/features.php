<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Features Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Config
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

return [
	// Determines which features are available in a deployment

	// Post views
	'views' => [
		'map' => TRUE,
		'list' => TRUE,
		'chart' => FALSE,
		'timeline' => FALSE,
	],

	// Data sources
	'data-providers' => [
		'smssync' => TRUE,
		'twitter' => TRUE,
		'frontlinesms' => FALSE,
		'email' => FALSE,
		'twilio' => FALSE,
		'nexmo' => FALSE,
	],
];
