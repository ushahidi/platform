<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Entity;
use Ushahidi\Modules\V5\Models\Notification;
use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\Acl as AccessControlList;

class NotificationPolicy
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
        $empty_notification_entity = new Entity\Notification();
        return $this->isAllowed($empty_notification_entity, 'search');
    }

    /**
     *
     * @param GenericUser $user
     * @param Notification $notification
     * @return bool
     */
    public function show(User $user, Notification $notification)
    {
        $notification_entity = new Entity\Notification($notification->toArray());
        return $this->isAllowed($notification_entity, 'read');
    }

    /**
     *
     * @param GenericUser $user
     * @param Notification $notification
     * @return bool
     */
    public function delete(User $user, Notification $notification)
    {
        $notification_entity = new Entity\Notification($notification->toArray());
        return $this->isAllowed($notification_entity, 'delete');
    }
    /**
     * @param Notification $notification
     * @return bool
     */
    public function update(User $user, Notification $notification)
    {
        // we convert to a Notification entity to be able to continue using the old authorizers and classes.
        $notification_entity = new Entity\Notification($notification->toArray());
        return $this->isAllowed($notification_entity, 'update');
    }


    /**
     * @param Notification $notification
     * @return bool
     */
    public function store(User $user, Notification $notification)
    {
        // we convert to a notification_entity entity to be able to continue using the old authorizers and classes.
        $notification_entity = new Entity\Notification($notification->toArray());
        return $this->isAllowed($notification_entity, 'create');
    }


    /**
     * @param $entity
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($entity, $privilege)
    {
        $authorizer = service('authorizer.notification');

        // These checks are run within the user context.
        $user = $authorizer->getUser();

         // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        // Admin is allowed access to everything
        if ($this->isUserAdmin($user)) {
            return true;
        }

        // Allow create, read, update and delete if owner.
        if ($this->isUserOwner($entity, $user)
            and in_array($privilege, ['create', 'read', 'update', 'delete'])) {
            return true;
        }

        // Logged in users can subscribe to and search notifications
        if ($user->getId() and in_array($privilege, ['search'])) {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }
}
