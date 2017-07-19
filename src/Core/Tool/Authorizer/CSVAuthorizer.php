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
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Tool\Permissions\AclTrait;
use Ushahidi\Core\Traits\DataImportAccess;

class CSVAuthorizer implements Authorizer
{
	use UserContext;

	// It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;

	// Check if user has Admin access
	use AdminAccess;

	// Check that the user has the necessary permissions
	// if roles are available for this deployment.
	use AclTrait;

	// Check if the user can import data
	use DataImportAccess;

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// Check if the user can import data first
		if (!$this->canImportData()) {
			return false;
		}

		// These checks are run within the user context.
		$user = $this->getUser();

		// Allow role with the right permissions
		if ($this->acl->hasPermission($user, Permission::DATA_IMPORT)) {
			return true;
		}

		// Allow admin access
		if ($this->isUserAdmin($user)) {
			return true;
		}

		return false;
	}
}
