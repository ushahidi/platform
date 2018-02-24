<?php

/**
 * Ushahidi Admin Access Trait
 *
 * Gives objects one new method:
 * `isUserAdmin(User $user)`
 *
 * This checks if `$user` has an admin role
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use Ushahidi\Core\Entity\User;

trait AdminAccess
{

	/**
	 * Check if the user has an Admin role
	 * @param  User    $user
	 * @return boolean
	 */
	protected function  isUserAdmin(User $user)
	{
		return ($user->id && $user->role === 'admin');
	}
}
