<?php

/**
 * Ushahidi Config Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tool\Authorizer;

use Ushahidi\Entity;
use Ushahidi\Entity\Config;
use Ushahidi\Entity\User;
use Ushahidi\Entity\UserRepository;
use Ushahidi\Tool\Authorizer;
use Ushahidi\Traits\AdminAccess;
use Ushahidi\Traits\EnsureUserEntity;

// The `ConfigAuthorizer` class is responsible for access checks on `Config` Entities
class ConfigAuthorizer implements Authorizer
{
	// It uses the EnsureUserEntity trait to load users if needed
	use EnsureUserEntity;

	// It uses `AdminAccess` to check if the user has admin access
	use AdminAccess;

	/**
	 * Public config groups
	 * @var [string, ...]
	 */
	protected $public_groups = ['features', 'map', 'site'];

	/**
	 * @param UserRepository $user_repo
	 */
	public function __construct(UserRepository $user_repo)
	{
		$this->user_repo = $user_repo;
	}

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege, $user = null)
	{
		// First we check we've got a `User` Entity.
		$this->ensureUserIsEntity($user);

		// Then we check if a user has the 'admin' role. If they do they're
		// allowed access to everything (all entities and all privileges)
		if ($this->isUserAdmin($user)) {
			return true;
		}

		// If a config group is public then *anyone* can view it.
		if ($privilege === 'get' && $this->isConfigPublic($entity)) {
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
		if (in_array($entity->getGroup(), $this->public_groups)) {
			return true;
		}

		return false;
	}
}
