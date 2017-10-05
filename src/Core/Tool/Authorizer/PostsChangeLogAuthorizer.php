<?php

/**
 * Ushahidi PostsChangeLog Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\PostsChangeLog;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Traits\EnsureUserEntity;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\GuestAccess;
use Ushahidi\Core\Traits\OwnerAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\PrivateDeployment;
use Ushahidi\Core\Tool\Permissions\AclTrait;

// The `FormAttributeAuthorizer` class is responsible
// for access checks on Form Attributes
class PostsChangeLogAuthorizer implements Authorizer
{
  // The access checks are run under the context of a specific user
	use UserContext;

  // It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;

  // Check that the user has the necessary permissions
  // if roles are available for this deployment.
  use AclTrait;



	// - `AdminAccess` to check if the user has admin access
	use AdminAccess;



	// It uses `PrivateDeployment` to check whether a deployment is private
	use PrivateDeployment;


  /* Authorizer */
  public function isAllowed(Entity $entity, $privilege)
  {
    // These checks are run within the user context.
    $user = $this->getUser();

    // Allow role with the right permissions
    if ($this->acl->hasPermission($user, Permission::MANAGE_POSTS)) {
      return true;
    }


    // Allow admin access
    if ($this->isUserAdmin($user)) {
      return true;
    }
    //return false;
    //TODO: URGENT: CHANGE THIS TO FALSE!
      return true;

  }


}
