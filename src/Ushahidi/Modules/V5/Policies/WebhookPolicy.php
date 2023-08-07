<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Entity;
use Ushahidi\Modules\V5\Models\Webhook\Webhook;
use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\Acl as AccessControlList;

class WebhookPolicy
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
        $empty_webhook_entity = new Entity\Webhook();
        return $this->isAllowed($empty_webhook_entity, 'search');
    }

    /**
     *
     * @param GenericUser $user
     * @param Webhook $webhook
     * @return bool
     */
    public function show(User $user, Webhook $webhook)
    {
        $webhook_entity = new Entity\Webhook($webhook->toArray());
        return $this->isAllowed($webhook_entity, 'read');
    }

    /**
     *
     * @param GenericUser $user
     * @param Webhook $webhook
     * @return bool
     */
    public function delete(User $user, Webhook $webhook)
    {
        $webhook_entity = new Entity\Webhook($webhook->toArray());
        return $this->isAllowed($webhook_entity, 'delete');
    }
    /**
     * @param Webhook $webhook
     * @return bool
     */
    public function update(User $user, Webhook $webhook)
    {
        // we convert to a Webhook entity to be able to continue using the old authorizers and classes.
        $webhook_entity = new Entity\Webhook($webhook->toArray());
        return $this->isAllowed($webhook_entity, 'update');
    }


    /**
     * @param Webhook $webhook
     * @return bool
     */
    public function store(User $user, Webhook $webhook)
    {
        // we convert to a webhook_entity entity to be able to continue using the old authorizers and classes.
        $webhook_entity = new Entity\Webhook($webhook->toArray());
        return $this->isAllowed($webhook_entity, 'create');
    }


    /**
     * @param $entity
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($entity, $privilege)
    {
        $authorizer = service('authorizer.webhook');

        // These checks are run within the user context.
        $user = $authorizer->getUser();

        // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        // Then we check if a user has the 'admin' role. If they do they're
        // allowed access to everything (all entities and all privileges)
        if ($this->isUserAdmin($user)) {
            return true;
        }


        // Allow create, read and update if owner.
        // Webhooks should not be deleted.
        if ($this->isUserOwner($entity, $user)
            and in_array($privilege, ['create', 'read', 'update'])) {
            return true;
        }

        // Logged in users can read and search webhooks
        if ($user->getId() and in_array($privilege, ['read','search'])) {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }
}
