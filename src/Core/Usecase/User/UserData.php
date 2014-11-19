<?php

/**
 * Ushahidi Platform User Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\User;

use Ushahidi\Core\Data;

class UserData extends Data
{
	public $id;
	public $email;
	public $realname;
	public $username;
	public $password;
	public $role = 'user';
	public $logins;
	public $failed_attempts;
	public $last_login;
	public $last_attempt;
	public $created;
	public $updated;
}
