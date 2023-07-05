<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\Acl as AccessControlList;
use Ushahidi\Modules\V5\Models\Message as EloquentMessage;
use Ushahidi\Core\Ohanzee\Entities\Message as OhanzeeMessage;

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

    private function getEntity(EloquentMessage $message)
    {
        $data = $message->toArray();
        $data['contact_type'] = $data['contact']['type'];
        $data['contact'] = $data['contact']['contact'];
        return new OhanzeeMessage($data);
    }

    public function index()
    {
        $empty_message_entity = new OhanzeeMessage();
        return $this->isAllowed($empty_message_entity, 'search');
    }

    public function show(User $user, EloquentMessage $message)
    {
        $message_entity = $this->getEntity($message);
        return $this->isAllowed($message_entity, 'read');
    }

    public function delete(User $user, EloquentMessage $message)
    {
        $message_entity = new OhanzeeMessage($message->toArray());
        return $this->isAllowed($message_entity, 'delete');
    }

    public function update(User $user, EloquentMessage $message)
    {
        // we convert to a Message entity to be able to continue using the old authorizers and classes.
        $message_entity = $this->getEntity($message);
        return $this->isAllowed($message_entity, 'update');
    }

    public function store(User $user, EloquentMessage $message)
    {
        // we convert to a message_entity entity to be able to continue using the old authorizers and classes.
        $message_entity = new OhanzeeMessage($message->toArray());
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
