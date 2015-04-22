<?php

/**
 * Ushahidi Set Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\Set;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\OwnerAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PrivAccess;

// The `SetAuthorizer` class is responsible for access checks on `Sets`
class SetAuthorizer implements Authorizer
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// It uses methods from several traits to check access:
	// - `OwnerAccess` to check if a user owns the set
	// - `AdminAccess` to check if the user has admin access
	use AdminAccess, OwnerAccess;

	// It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;

	protected function isVisibleToUser(Set $entity, $user)
	{
		if ($entity->visible_to) {
			return in_array($user->role, $entity->visible_to);
		}

		// If no roles are selected, the Set is considered completely public.
		return true;
	}

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// These checks are run within the user context.
		$user = $this->getUser();

		// Then we check if a user has the 'admin' role. If they do they're
		// allowed access to everything (all entities and all privileges)
		if ($this->isUserAdmin($user)) {
			return true;
		}

		// Non-admin users are not allowed to make sets featured
		if (in_array($privilege, ['create', 'update']) and $entity->hasChanged('featured')) {
			return false;
		}

		// If the user is the owner of this set, they can do anything
		if ($this->isUserOwner($entity, $user)) {
			return true;
		}

		// Check if the Set is only visible to specific roles.
		if ($this->isVisibleToUser($entity, $user) and $privilege === 'read') {
			return true;
		}

		// All *logged in* users can create sets
		if ($user->getId() and $privilege === 'create') {
			return true;
		}

		// Finally, all users can search sets
		if ($privilege === 'search') {
			return true;
		}

		// If no other access checks succeed, we default to denying access
		return false;
	}
}
