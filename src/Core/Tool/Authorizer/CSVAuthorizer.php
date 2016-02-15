<?php

/**
 * Ushahidi CSV Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\CSV;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Tool\Permissions\Acl;
use Ushahidi\Core\Tool\Permissions\Permissionable;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\PermissionAccess;

class CSVAuthorizer implements Authorizer, Acl
{
	use UserContext;

	// It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;
	
	// Check if user has Admin access
	use AdminAccess;

	// Check that the user has the necessary permissions
	use PermissionAccess;

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// These checks are run within the user context.
		$user = $this->getUser();

		// Allow role with the right permissions
		if ($entity instanceof Permissionable and
			$this->hasPermission($user, $entity->getPermissions())) {
			return true;
		}
		
		// Allow admin access
		if ($this->isUserAdmin($user)) {
			return true;
		}

		return false;
	}
}
