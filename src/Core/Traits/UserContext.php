<?php

/**
 * Ushahidi User Context Trait
 *
 * Gives objects methods for setting and retrieving the user context.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use Ushahidi\Core\Entity\User;

trait UserContext
{
	// storage for the user
	protected $user;

	/**
	 * Set the user context.
	 * @param  User $user  set the context
	 * @return void
	 */
	public function setUser(User $user)
	{
		$this->user = $user;
	}

	/**
	 * Get the user context.
	 * @return User
	 */
	public function getUser()
	{
		if (!$this->user) {
			throw new RuntimeException('Cannot get the user context before it has been set');
		}

		return $this->user;
	}

	/**
	 * Get the userid for this context.
	 * @return Integer
	 */
	public function getUserId()
	{
		return $this->user->id;
	}
}
