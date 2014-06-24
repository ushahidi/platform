<?php

/**
 * Ushahidi Platform User Registration Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\User;

use Ushahidi\Data;

class RegisterData extends Data
{
	public $username;
	public $password;
	public $email;
}
