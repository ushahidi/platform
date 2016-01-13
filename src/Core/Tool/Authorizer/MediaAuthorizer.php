<?php

/**
 * Ushahidi Media Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\Media;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\EnsureUserEntity;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\GuestAccess;
use Ushahidi\Core\Traits\OwnerAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\PrivateDeployment;

// The `MediaAuthorizer` class is responsible for access checks on `Medias`
class MediaAuthorizer implements Authorizer
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// It uses methods from several traits to check access:
	// - `AdminAccess` to check if the user has admin access
	// - `OwnerAccess` to check if a user owns the entity
	use AdminAccess, OwnerAccess;

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

		// All users are allowed to view and create new media files.
		if (in_array($privilege, ['read', 'create', 'search'])) {
			return true;
		}

		// Owners can removed media they own.
		if ($this->isUserOwner($entity, $user) && $privilege === 'delete') {
			return true;
		}

		// If no other access checks succeed, we default to denying access
		return false;
	}
}
