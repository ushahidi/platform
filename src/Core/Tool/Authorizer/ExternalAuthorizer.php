<?php

/**
 * Ushahidi Export Job Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\OwnerAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\PrivateDeployment;
use Ushahidi\Core\Tool\Permissions\AclTrait;

class ExternalAuthorizer implements Authorizer
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// To check whether the user has admin access
	use AdminAccess;

	// To check whether user owns the webhook
	use OwnerAccess;

	// It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;

	// It uses `PrivateDeployment` to check whether a deployment is private
	use PrivateDeployment;

	// Check that the user has the necessary permissions
	// if roles are available for this deployment.
	use AclTrait;


	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// The CLI and external auth tools do not provide a user at present
		// auth validation is performed via the verifier tool
		// with shared secret and api key
		return true;
	}
}
