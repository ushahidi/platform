<?php

/**
 * Ushahidi Platform User Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

use Ushahidi\Entity;

class User extends Entity
{
	public $id;
	public $email;
	public $realname;
	public $username;
	public $password;
	public $logins = 0;
	public $failed_attempts = 0;
	public $last_login;
	public $last_attempt;
	public $created;
	public $updated;
	public $role = 'user';

	public function getResource()
	{
		return 'users';
	}
}
