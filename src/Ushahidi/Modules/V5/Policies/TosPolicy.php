<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Core\Entity;
use Ushahidi\Modules\V5\Models\Tos;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;

class TosPolicy
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
   
    // To check whether user owns the webhook
    use OwnerAccess;


    protected $user;

    // It requires a `TagRepository` to load parent posts too.
    //protected $tag_repo;

    /**
     *
     * @return bool
     */
    public function index()
    {
        $empty_tos = new Entity\Tos();
        return $this->isAllowed($empty_tos, 'search');
    }

    /**
     *
     * @param Tos $tos
     * @return bool
     */
    public function show(Tos $tos)
    {
        $tos = new Entity\Tos($tos->toArray());
        return $this->isAllowed($tos, 'read');
    }

    /**
     *
     * @param Tos $tos
     * @return bool
     */
    public function delete(Tos $tos)
    {
        $tos = new Entity\Tos($tos->toArray());
        return $this->isAllowed($tos, 'delete');
    }
    /**
     * @param Tos $tos
     * @return bool
     */
    public function update(Tos $tos)
    {
        // we convert to a form entity to be able to continue using the old authorizers and classes.
        $tos = new Entity\Tos($tos->toArray());
        return $this->isAllowed($tos, 'update');
    }


    /**
     * @param Tos $tos
     * @return bool
     */
    public function store()
    {
        // we convert to a form entity to be able to continue using the old authorizers and classes.
        $tos = new Entity\Tos();
        return $this->isAllowed($tos, 'create');
    }
    /**
     * @param $entity
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($entity, $privilege)
    {

        $authorizer = service('authorizer.tos');

        // These checks are run within the user context.
        $user = $authorizer->getUser();

        //if user is not actual user, but is in fact anonymous
        if (($privilege === 'search' || $privilege === 'create')
            && $this->isUserAndOwnerAnonymous($entity, $user)) {
            return false;
        }

        // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        if ($user->getId() and $privilege === 'create') {
            return true;
        }

        if ($user->getId() and $privilege === 'search') {
            return true;
        }

        if ($privilege === 'read' && $entity->user_id === $user->id) {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }
}
