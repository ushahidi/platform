<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Entity;
use Ushahidi\Modules\V5\Models\ExportJob;
use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\Acl as AccessControlList;

class ExportJobPolicy
{

    // The access checks are run under the context of a specific user
    use UserContext;

    // It uses methods from several traits to check access:
    // - `AdminAccess` to check if the user has admin access
    use AdminAccess;

    // It uses `PrivAccess` to provide the `getAllowedPrivs` method.
    use PrivAccess;

    // It uses `PrivateDeployment` to check whether a deployment is private
    use PrivateDeployment;

    // Check that the user has the necessary permissions
    use AccessControlList;

    use OwnerAccess;

    protected $user;


    /**
     *
     * @param  \Ushahidi\Modules\User  $user
     * @return bool
     */
    public function index()
    {
        $empty_export_job_entity = new Entity\ExportJob();
        return $this->isAllowed($empty_export_job_entity, 'search');
    }

    /**
     *
     * @param GenericUser $user
     * @param ExportJob $export_job
     * @return bool
     */
    public function show(User $user, ExportJob $export_job)
    {
        $export_job_entity = new Entity\ExportJob($export_job->toArray());
        return $this->isAllowed($export_job_entity, 'read');
    }

    /**
     *
     * @param GenericUser $user
     * @param ExportJob $export_job
     * @return bool
     */
    public function delete(User $user, ExportJob $export_job)
    {
        $export_job_entity = new Entity\ExportJob($export_job->toArray());
        return $this->isAllowed($export_job_entity, 'delete');
    }
    /**
     * @param ExportJob $export_job
     * @return bool
     */
    public function update(User $user, ExportJob $export_job)
    {
        // we convert to a ExportJob entity to be able to continue using the old authorizers and classes.
        $export_job_entity = new Entity\ExportJob($export_job->toArray());
        return $this->isAllowed($export_job_entity, 'update');
    }


    /**
     * @param ExportJob $export_job
     * @return bool
     */
    public function store(User $user, ExportJob $export_job)
    {
        // we convert to a export_job_entity entity to be able to continue using the old authorizers and classes.
        $export_job_entity = new Entity\ExportJob($export_job->toArray());
        return $this->isAllowed($export_job_entity, 'create');
    }


    /**
     * @param $entity
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($entity, $privilege)
    {
        $authorizer = service('authorizer.export_job');

        // These checks are run within the user context.
        $user = $authorizer->getUser();

        // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        // First check whether there is a role with the right permissions
        if ($authorizer->acl->hasPermission($user, Permission::DATA_IMPORT_EXPORT) or
            $authorizer->acl->hasPermission($user, Permission::LEGACY_DATA_IMPORT)
        ) {
            return true;
        }

        // First check whether there is a role with the right permissions
        if ($authorizer->acl->hasPermission($user, Permission::MANAGE_POSTS)) {
            return true;
        }

        // Admin is allowed access to everything
        if ($this->isUserAdmin($user)) {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }
}
