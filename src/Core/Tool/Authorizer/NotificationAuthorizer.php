<?php

/**
 * Ushahidi Notification Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\OwnerAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\PrivateDeployment;

class NotificationAuthorizer implements Authorizer
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// To check whether the user has admin access
	use AdminAccess;

	// To check whether user owns the notification
	use OwnerAccess;

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

		// Admin is allowed access to everything
		if ($this->isUserAdmin($user)) {
			return true;
		}
		
		// Allow create, read, update and delete if owner.
		if ($this->isUserOwner($entity, $user)
			and in_array($privilege, ['create', 'read', 'update', 'delete'])) {

			return true;
		}

		// Logged in users can subscribe to and search notifications
		if ($user->getId() and in_array($privilege, ['search'])) {
			return true;
		}

		// If no other access checks succeed, we default to denying access
		return false;
	}
}
