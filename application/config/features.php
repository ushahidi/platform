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

  // Client limits
  // Where TRUE is infinite and an integer defines a limit
  'limits' => [
    'posts' => TRUE,
    'forms' => TRUE,
    'admin_users' => TRUE,
  ],
];
