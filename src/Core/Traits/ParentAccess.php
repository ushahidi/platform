<?php

/**
 * Ushahidi Parent Access Trait
 *
 * Gives objects one new method:
 * `isAllowedParent(Entity $entity, $privilege, User $user)`
 *
 * This checks if the user has `$privilege` on the parent entity.
 * Defaults to returning `true` if the entity has no parent.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Repository;
use Ushahidi\Core\Entity\User;

// The `ParentAccess` trait checks for access to a parent entity
trait ParentAccess
{
	/**
	 * Check if $user has access to the parent of $entity
	 * @param  Entity  $entity
	 * @param  User    $user
	 * @return boolean
	 */
	protected function isAllowedParent(Entity $entity, $privilege)
	{
		// If `$entity` has a parent..
		if ($parent = $this->getParent($entity)) {
			// .. we run access checks on that entity too.
			// If we can't access the parent, we can't access this entity
			return $this->isAllowed($parent, $privilege);
		}

		return true;
	}

	// This trait requires users implement a `getParent` method to load
	// parent entities
	/**
	 * Load the parent entity (if there is one)
	 * @param  Entity $entity
	 * @return Entity|false
	 */
	abstract protected function getParent(Entity $entity);

	// Since this trait should only be used by `Authorizer` classes we require
	// an isAllowed method with the same signature as the `Authorizer` interface
	abstract public function isAllowed(Entity $entity, $privilege);
}
