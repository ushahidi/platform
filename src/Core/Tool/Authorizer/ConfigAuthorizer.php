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
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PrivAccess;

// The `ConfigAuthorizer` class is responsible for access checks on `Config` Entities
class ConfigAuthorizer implements Authorizer
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// It uses `AdminAccess` to check if the user has admin access
	use AdminAccess;

	// It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;

	/**
	 * Public config groups
	 * @var [string, ...]
	 */
	protected $public_groups = ['features', 'map', 'site'];

	/**
	 * Public config groups
	 * @var [string, ...]
	 */
	protected $writable_groups = ['map', 'site', 'dataprovider'];

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// These checks are run within the `User` context.
		$user = $this->getUser();

		// Then we check if a user has the 'admin' role.
		if ($this->isUserAdmin($user)) {
			if (in_array($privilege, ['create', 'update']) && !$this->isConfigWritable($entity)) {
				// If a config group is *not* writable, even an admin cannot edit it
				return false;
			} else {
				// .. but an admin can do *anything* else
				return true
			}
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
	 * Check if a config group is writable
	 * @param  Entity  $entity
	 * @return boolean
	 */
	protected function isConfigWriteable(Config $entity)
	{
		// Config that is unloaded is treated as writable.
		if (!$entity->getId()) {
			return true;
		}

		if (in_array($entity->getId(), $this->writable_groups)) {
			return true;
		}

		return false;
	}
}
