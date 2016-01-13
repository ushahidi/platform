<?php

/**
 * Ushahidi Layer Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\PrivateDeployment;

// The `LayerAuthorizer` class is responsible for access checks on `Layer` Entities
class LayerAuthorizer implements Authorizer
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// It uses `AdminAccess` to check if the user has admin access
	use AdminAccess;

	// It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;

	// It uses `PrivateDeployment` to check whether a deployment is private
	use PrivateDeployment;

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// These checks are run within the user context.
		$user = $this->getUser();

		// Only logged in users have access if the deployment is private
		if (!$this->hasAccess()) {
			return false;
		}
		
		// Then we check if a user has the 'admin' role. If they do they're
		// allowed access to everything (all entities and all privileges)
		if ($this->isUserAdmin($user)) {
			return true;
		}

		// If a post is active then *anyone* can view it.
		// Only an admin can view inactive layers or create/edit/update layers
		if ($privilege === 'read' && $this->isLayerActive($entity)) {
			return true;
		}

		// If no other access checks succeed, we default to denying access
		return false;
	}

	/**
	 * Check if a layer is active
	 * @param  Entity  $entity
	 * @return boolean
	 */
	protected function isLayerActive(Entity $entity)
	{
		// To check if a layer is active we just check the post 'active' flag
		if ($entity->active) {
			return true;
		}

		return false;
	}
}
