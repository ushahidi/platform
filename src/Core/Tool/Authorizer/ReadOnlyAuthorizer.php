<?php

/**
 * Ushahidi read only authorizer
 * 
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PrivAccess;

class ReadOnlyAuthorizer implements Authorizer
{

	// The access checks are run under the context of a specific user
	use UserContext;

	// It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// Only allow searching and viewing
		if (in_array($privilege, ['search', 'read'])) {
			return true;
		}

		return false;
	}
}
