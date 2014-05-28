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
	'default_provider' => array(
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
	)
);
