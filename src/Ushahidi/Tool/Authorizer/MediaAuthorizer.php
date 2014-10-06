<?php

/**
 * Ushahidi Media Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tool\Authorizer;

use Ushahidi\Entity;
use Ushahidi\Entity\User;
use Ushahidi\Entity\Media;
use Ushahidi\Tool\Authorizer;
use Ushahidi\Traits\EnsureUserEntity;
use Ushahidi\Traits\AdminAccess;
use Ushahidi\Traits\GuestAccess;
use Ushahidi\Traits\OwnerAccess;
use Ushahidi\Traits\UserContext;
use Ushahidi\Traits\PrivAccess;

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

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// These checks are run within the user context.
		$user = $this->getUser();

		// Then we check if a user has the 'admin' role. If they do they're
		// allowed access to everything (all entities and all privileges)
		if ($this->isUserAdmin($user)) {
			return true;
		}

		// All users are allowed to view and create new media files.
		if (in_array($privilege, ['read', 'create'])) {
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
