<?php

/**
 * Ushahidi Config Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Config;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Tool\Permissions\Acl;
use Ushahidi\Core\Tool\Permissions\Permissionable;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\PermissionAccess;
use Ushahidi\Core\Traits\Permissions\ManageSettings;

// The `ConfigAuthorizer` class is responsible for access checks on `Config` Entities
class ConfigAuthorizer implements Authorizer, Permissionable
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// It uses `AdminAccess` to check if the user has admin access
	use AdminAccess;

	// It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;

	// Check that the user has the necessary permissions
	// if roles are available for this deployment.
	use PermissionAccess;

	// Provides `getPermission`
	use ManageSettings;

	/**
	 * Public config groups
	 * @var [string, ...]
	 */
	protected $public_groups = ['features', 'map', 'site'];

	/**
	 * Public config groups
	 * @var [string, ...]
	 */
	protected $readonly_groups = ['features'];

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// These checks are run within the `User` context.
		$user = $this->getUser();

		// If a config group is read only *no one* can edit it (not even admin)
		if (in_array($privilege, ['create', 'update']) && $this->isConfigReadOnly($entity)) {
			return false;
		}

		// Allow role with the right permissions to do everything else
		if ($this->hasPermission($user)) {
			return true;
		}

		// If a user has the 'admin' role, they can do pretty much everything else
		if ($this->isUserAdmin($user)) {
			return true;
		}

		// If a config group is public then *anyone* can view it.
		if (in_array($privilege, ['read', 'search']) && $this->isConfigPublic($entity)) {
			return true;
		}

		// If no other access checks succeed, we default to denying access
		return false;
	}

	/**
	 * Check if a config group is public
	 * @param  Entity  $entity
	 * @return boolean
	 */
	protected function isConfigPublic(Config $entity)
	{
		// Config that is unloaded is treated as public.
		if (!$entity->getId()) {
			return true;
		}

		if (in_array($entity->getId(), $this->public_groups)) {
			return true;
		}

		return false;
	}

	/**
	 * Check if a config group is read only
	 * @param  Entity  $entity
	 * @return boolean
	 */
	protected function isConfigReadOnly(Config $entity)
	{
		// Config that is unloaded is treated as writable.
		if (!$entity->getId()) {
			return false;
		}

		if (in_array($entity->getId(), $this->readonly_groups)) {
			return true;
		}

		return false;
	}
}
