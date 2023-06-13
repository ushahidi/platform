<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Entity;
use Ushahidi\Modules\V5\Models\Message;
use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\Acl as AccessControlList;

class MessagePolicy
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

    private function getEntity(Message $message)
    {
        $data = $message->toArray();
        $data['contact_type'] = $data['contact']['type'];
        $data['contact'] = $data['contact']['contact'];
        return new Entity\Message($data);
    }
    /**
     *
     * @param  \Ushahidi\Modules\User  $user
     * @return bool
     */
    public function index()
    {
        $empty_message_entity = new Entity\Message();
        return $this->isAllowed($empty_message_entity, 'search');
    }

    /**
     *
     * @param GenericUser $user
     * @param Message $message
     * @return bool
     */
    public function show(User $user, Message $message)
    {
        $message_entity = $this->getEntity($message);
        return $this->isAllowed($message_entity, 'read');
    }

    /**
     *
     * @param GenericUser $user
     * @param Message $message
     * @return bool
     */
    public function delete(User $user, Message $message)
    {
        $message_entity = new Entity\Message($message->toArray());
        return $this->isAllowed($message_entity, 'delete');
    }
    /**
     * @param Message $message
     * @return bool
     */
    public function update(User $user, Message $message)
    {
        // we convert to a Message entity to be able to continue using the old authorizers and classes.
        $message_entity = new Entity\Message($message->toArray());
        return $this->isAllowed($message_entity, 'update');
    }


    /**
     * @param Message $message
     * @return bool
     */
    public function store(User $user, Message $message)
    {
        // we convert to a message_entity entity to be able to continue using the old authorizers and classes.
        $message_entity = new Entity\Message($message->toArray());
        return $this->isAllowed($message_entity, 'create');
    }


    /**
     * @param $entity
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($entity, $privilege)
    {
        $authorizer = service('authorizer.message');

        // These checks are run within the user context.
        $user = $authorizer->getUser();

       // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

    // Incoming messages cannot be updated
        if ($privilege === 'update' && $this->isMessageIncoming($entity)) {
            return false;
        }

    // Check whether there is a role with the right permissions
        if ($authorizer->acl->hasPermission($user, Permission::MANAGE_POSTS)) {
            return true;
        }

    // Then we check if a user has the 'admin' role. If they do they're
    // allowed access to everything (all entities and all privileges)
        if ($this->isUserAdmin($user)) {
            return true;
        }

        if ($privilege === 'receive') {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }

    protected function isMessageIncoming($entity)
    {
        return $entity->direction === 'incoming';
    }
}
