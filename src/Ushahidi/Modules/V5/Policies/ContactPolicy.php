<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Entity;
use Ushahidi\Modules\V5\Models\Contact;
use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\Acl as AccessControlList;

class ContactPolicy
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
        $empty_contact_entity = new Entity\Contact();
        return $this->isAllowed($empty_contact_entity, 'search');
    }

    /**
     *
     * @param GenericUser $user
     * @param Contact $contact
     * @return bool
     */
    public function show(User $user, Contact $contact)
    {
        $contact_entity = new Entity\Contact($contact->toArray());
        return $this->isAllowed($contact_entity, 'read');
    }

    /**
     *
     * @param GenericUser $user
     * @param Contact $contact
     * @return bool
     */
    public function delete(User $user, Contact $contact)
    {
        $contact_entity = new Entity\Contact($contact->toArray());
        return $this->isAllowed($contact_entity, 'delete');
    }
    /**
     * @param Contact $contact
     * @return bool
     */
    public function update(User $user, Contact $contact)
    {
        // we convert to a Contact entity to be able to continue using the old authorizers and classes.
        $contact_entity = new Entity\Contact($contact->toArray());
        return $this->isAllowed($contact_entity, 'update');
    }


    /**
     * @param Contact $contact
     * @return bool
     */
    public function store(User $user, Contact $contact)
    {
        // we convert to a contact_entity entity to be able to continue using the old authorizers and classes.
        $contact_entity = new Entity\Contact($contact->toArray());
        return $this->isAllowed($contact_entity, 'create');
    }


    /**
     * @param $entity
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($entity, $privilege)
    {
        $authorizer = service('authorizer.contact');

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

        // Users can delete their own contacts
        if ($this->isUserOwner($entity, $user)) {
            return true;
        }

        // Logged in users can read and search contacts
        if ($user->getId() and in_array($privilege, ['read','search'])) {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }
}
