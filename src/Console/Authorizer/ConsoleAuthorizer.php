<?php

/**
 * Ushahidi Console Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Console\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\UserContext;

// The `ConsoleAuthorizer` class is responsible for access checks for console tasks
class ConsoleAuthorizer implements Authorizer
{
	// The access checks are run under the context of a specific user
	// @todo refactor to avoid including this. CLI doesn't have a user context
	use UserContext;

	// It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// These checks are run within the user context.
		$user = $this->getUser();

		// Only logged in users have access if the deployment is private
		if (!$this->canAccessDeployment($user)) {
			return false;
		}

		// User should not be able to delete self
		if ($privilege === 'delete' && $this->isUserSelf($entity)) {
			return false;
		}

		// Role with the Manage Users permission can manage all users
		if ($this->acl->hasPermission($user, Permission::MANAGE_USERS)) {
			return true;
		}

		// Admin user should be able to do anything - short of deleting self
		if ($this->isUserAdmin($user)) {
			return true;
		}

		// User cannot change their own role
		if ('update' === $privilege && $this->isUserSelf($entity) && $entity->hasChanged('role')) {
			return false;
		}

		// Regular user should be able to update and read_full only self
		if ($this->isUserSelf($entity) && in_array($privilege, ['update', 'read_full'])) {
			return true;
		}

		// Regular user can always read
		if (in_array($privilege, ['read', 'search'])) {
			return true;
		}

		// Users should always be allowed to register
		if ($privilege === 'register') {
			return true;
		}

		// If no other access checks succeed, we default to denying access
		return false;
	}
}
