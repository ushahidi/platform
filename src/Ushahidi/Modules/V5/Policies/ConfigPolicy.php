<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Entity\Config as EntityConfig;
use Ushahidi\Contracts\Entity;
use Ushahidi\Modules\V5\Models\Config;
use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\Acl as AccessControlList;

class ConfigPolicy
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






    /**
     *
     * @param  \Ushahidi\Modules\User  $user
     * @return bool
     */
    public function index()
    {
        $empty_config_entity = new EntityConfig();
        return $this->isAllowed($empty_config_entity, 'search');
    }

    /**
     *
     * @param GenericUser $user
     * @param Config $config
     * @return bool
     */
    public function show(User $user, Config $config)
    {
        $config_entity = new EntityConfig($config->toArray());
        return $this->isAllowed($config_entity, 'read');
    }

    /**
     *
     * @param GenericUser $user
     * @param Config $config
     * @return bool
     */
    public function delete(User $user, Config $config)
    {
        $config_entity = new EntityConfig($config->toArray());
        return $this->isAllowed($config_entity, 'delete');
    }
    /**
     * @param Config $config
     * @return bool
     */
    public function update(User $user, Config $config)
    {
        // we convert to a Config entity to be able to continue using the old authorizers and classes.
        $config_entity = new EntityConfig($config->toArray());
        return $this->isAllowed($config_entity, 'update');
    }


    /**
     * @param Config $config
     * @return bool
     */
    public function store(User $user, Config $config)
    {
        // we convert to a config_entity entity to be able to continue using the old authorizers and classes.
        $config_entity = new EntityConfig($config->toArray());
        return $this->isAllowed($config_entity, 'create');
    }


    /**
     * @param $entity
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($entity, $privilege)
    {
        $authorizer = service('authorizer.config');

        // These checks are run within the user context.
        $user = $authorizer->getUser();

        // If a config group is read only *no one* can edit it (not even admin)
        if (in_array($privilege, ['create', 'update']) && $this->isConfigReadOnly($entity)) {
            return false;
        }

        // Allow role with the right permissions to do everything else
        if ($authorizer->acl->hasPermission($user, Permission::MANAGE_SETTINGS)) {
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
     * @param  EntityBase  $entity
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
     * @param  EntityBase  $entity
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
