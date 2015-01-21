<?php

/**
 * Ushahidi User Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PrivAccess;

// The `UserAuthorizer` class is responsible for access checks on `Users`
class UserAuthorizer implements Authorizer
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// - `AdminAccess` to check if the user has admin access
	use AdminAccess;

	// It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;

	/**
	 * Checks if currently logged in user is the same as passed entity
	 * @param  User    $entity entity to check
	 * @param  User    $user   currently logged in user
	 * @return boolean
	 */
	protected function isUserSelf(User $entity, User $user)
	{
		return ($entity->id === $user->id);
	}

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// These checks are run within the user context.
		$user = $this->getUser();

		// User should not be able to delete self
		if ($privilege === 'delete' && $this->isUserSelf($entity, $user)) {
			return false;
		}

		// Admin user should be able to do anything - short of deleting self
		if ($this->isUserAdmin($user)) {
			return true;
		}

		// User cannot change their own role
		if ('update' === $privilege && $this->isUserSelf($entity, $user) && $entity->hasChanged('role')) {
			return false;
		}

		// Regular user should be able to update and read only self
		if ($this->isUserSelf($entity, $user) && in_array($privilege, ['read', 'update'])) {
			return true;
		}

		// If no other access checks succeed, we default to denying access
		return false;
	}
}
