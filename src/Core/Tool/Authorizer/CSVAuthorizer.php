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
use Ushahidi\Core\Tool\Acl;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\PermissionAccess;
use Ushahidi\Core\Traits\DataImport;

class CSVAuthorizer implements Authorizer, Acl
{
	use UserContext;

	// It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;
	
	// Check if user has Admin access
	use AdminAccess;

	// Get required permissions
	use DataImport;

	// Check that the user has the necessary permissions
	use PermissionAccess;

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// These checks are run within the user context.
		$user = $this->getUser();

		// Allow role with the right permissions
		if ($this->hasPermission($user->role, $this->getRequiredPermissions())) {
			return true;
		}
		
		// Allow admin access
		if ($this->isUserAdmin($user)) {
			return true;
		}

		return false;
	}
}
