<?php

/**
 * Ushahidi Config Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Permission;
use Ushahidi\Contracts\Authorizer;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\Acl as AccessControlList;

// The `ConfigAuthorizer` class is responsible for access checks on `Config` Entities
class ConfigAuthorizer implements Authorizer
{
    // The access checks are run under the context of a specific user
    use UserContext;

    // It uses `AdminAccess` to check if the user has admin access
    use AdminAccess;

    // It uses `PrivAccess` to provide the `getAllowedPrivs` method.
    use PrivAccess;

    // Check that the user has the necessary permissions
    // if roles are available for this deployment.
    use AccessControlList;

    /**
     * Public config groups
     * @var [string, ...]
     */
    protected $public_groups = ['features', 'map', 'site', 'deployment_id'];

    /**
     * Public config groups
     * @var [string, ...]
     */
    protected $readonly_groups = ['features', 'deployment_id'];

    /* Authorizer */
    public function isAllowed(Entity $entity, $privilege)
    {
        // These checks are run within the `User` context.
        $user = $this->getUser();

        // If a config group is read only *no one* can edit it (not even admin)
        if (in_array($privilege, ['create', 'update']) && $this->isConfigReadOnly($entity)) {
            return false;
        }

        // Allow role with the right permissions to do everything else
        if ($this->acl->hasPermission($user, Permission::MANAGE_SETTINGS)) {
            return true;
        }

        // If a user has the 'admin' role, they can do pretty much everything else
        if ($this->isUserAdmin($user)) {
            return true;
        }

        // If a config group is public then *anyone* can view it.
        if (in_array($privilege, ['read', 'search']) && $this->isConfigPublic($entity)) {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }

    /**
     * Check if a config group is public
     * @param  Entity  $entity
     * @return boolean
     */
    protected function isConfigPublic(Entity $entity)
    {
        // Config that is unloaded is treated as public.
        if (!$entity->getId()) {
            return true;
        }

        if (in_array($entity->getId(), $this->public_groups)) {
            return true;
        }

        return false;
    }

    /**
     * Check if a config group is read only
     * @param  Entity  $entity
     * @return boolean
     */
    protected function isConfigReadOnly(Entity $entity)
    {
        // Config that is unloaded is treated as writable.
        if (!$entity->getId()) {
            return false;
        }

        if (in_array($entity->getId(), $this->readonly_groups)) {
            return true;
        }

        return false;
    }
}
