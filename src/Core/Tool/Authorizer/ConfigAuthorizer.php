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
use Ushahidi\Core\Traits\SuperpoweredAccess;
use Ushahidi\Core\Traits\ClientContext;

// The `ConfigAuthorizer` class is responsible for access checks on `Config` Entities
class ConfigAuthorizer implements Authorizer
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// Use client context for access checks run under the context of a specific client
	use ClientContext;

	// It uses `AdminAccess` to check if the user has admin access
	use AdminAccess;

	// It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;

	// It uses `SuperpoweredAccess` to check if the client has superpowers
	use SuperpoweredAccess;

	/**
	 * Public config groups
	 * @var [string, ...]
	 */
	protected $public_groups = ['features', 'map', 'site'];

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// First we run check with a `Client` context
		$client = $this->getClient();

		// If the client has superpowers it can do anything
		if ($this->hasSuperpowers($client)) {
			return true;
		}

		// These checks are run within the `User` context.
		$user = $this->getUser();

		// Then we check if a user has the 'admin' role. If they do they're
		// allowed access to everything (all entities and all privileges)
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
}
