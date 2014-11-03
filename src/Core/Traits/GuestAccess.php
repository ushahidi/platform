<?php

/**
 * Ushahidi Guest Access Trait
 *
 * Gives objects one new method:
 * `isUserGuest(User $user)`
 *
 * This checks if `$user` is not logged in.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\User;

trait GuestAccess
{
	/**
	 * Check if $user is unloaded or has the "guest" role
	 * @param  User    $user
	 * @return boolean
	 */
	protected function isUserGuest(User $user)
	{
		return (!$user->id || $user->role === 'guest');
	}
}
