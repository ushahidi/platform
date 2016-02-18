<?php

/**
 * Ushahidi Tag Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\Tag;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Tool\Permissions\Acl;
use Ushahidi\Core\Tool\Permissions\Permissionable;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\PrivateDeployment;
use Ushahidi\Core\Traits\PermissionAccess;
use Ushahidi\Core\Traits\Permissions\ManageSettings;

// The `TagAuthorizer` class is responsible for access checks on `Tags`
class TagAuthorizer implements Authorizer, Permissionable
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// - `AdminAccess` to check if the user has admin access
	use AdminAccess;

	// It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;

	// It uses `PrivateDeployment` to check whether a deployment is private
	use PrivateDeployment;

	// Check that the user has the necessary permissions
	// if roles are available for this deployment.
	use PermissionAccess;

	// Provides `getPermission`
	use ManageSettings;

	protected function isUserOfRole(Tag $entity, $user)
	{
		if ($entity->role) {
			return in_array($user->role, $entity->role);
		}

		// If no roles are selected, the Tag is considered completely public.
		return true;
	}

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// These checks are run within the user context.
		$user = $this->getUser();

		// Only logged in users have access if the deployment is private
		if (!$this->hasAccess()) {
			return false;
		}

		// First check whether there is a role with the right permissions
		if ($this->hasPermission($user)) {
			return true;
		}

		// Then we check if a user has the 'admin' role. If they do they're
		// allowed access to everything (all entities and all privileges)
		if ($this->isUserAdmin($user)) {
			return true;
		}

		// Finally, we check if the Tag is only visible to specific roles.
		if ($privilege === 'read' && $this->isUserOfRole($entity, $user)) {
			return true;
		}

		if ($privilege === 'search') {
			return true;
		}

		// If no other access checks succeed, we default to denying access
		return false;
	}
}
