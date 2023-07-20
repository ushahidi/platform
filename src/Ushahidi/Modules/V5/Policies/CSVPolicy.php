<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Entity;
use Ushahidi\Modules\V5\Models\CSV;
use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\Acl as AccessControlList;
use Ushahidi\Core\Facade\Feature;

class CSVPolicy
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
        $empty_csv_entity = new Entity\CSV();
        return $this->isAllowed($empty_csv_entity, 'search');
    }

    /**
     *
     * @param GenericUser $user
     * @param CSV $csv
     * @return bool
     */
    public function show(User $user, CSV $csv)
    {
        $csv_entity = new Entity\CSV($csv->toArray());
        return $this->isAllowed($csv_entity, 'read');
    }

    /**
     *
     * @param GenericUser $user
     * @param CSV $csv
     * @return bool
     */
    public function delete(User $user, CSV $csv)
    {
        $csv_entity = new Entity\CSV($csv->toArray());
        return $this->isAllowed($csv_entity, 'delete');
    }
    /**
     * @param CSV $csv
     * @return bool
     */
    public function update(User $user, CSV $csv)
    {
        // we convert to a CSV entity to be able to continue using the old authorizers and classes.
        $csv_entity = new Entity\CSV($csv->toArray());
        return $this->isAllowed($csv_entity, 'update');
    }


    /**
     * @param CSV $csv
     * @return bool
     */
    public function store(User $user, CSV $csv)
    {
        // we convert to a csv_entity entity to be able to continue using the old authorizers and classes.
        $csv_entity = new Entity\CSV($csv->toArray());
        return $this->isAllowed($csv_entity, 'create');
    }


    /**
     * @param $entity
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($entity, $privilege)
    {

        // Check if the user can import data first
        if (!Feature::isEnabled('data-import')) {
            return false;
        }

        $authorizer = service('authorizer.csv');

        // These checks are run within the user context.
        $user = $authorizer->getUser();

         // Allow role with the right permissions
        if ($authorizer->acl->hasPermission($user, Permission::DATA_IMPORT_EXPORT) or
         $authorizer->acl->hasPermission($user, Permission::LEGACY_DATA_IMPORT)) {
            return true;
        }

     // Allow admin access
        if ($this->isUserAdmin($user)) {
            return true;
        }

        return false;
    }
}
