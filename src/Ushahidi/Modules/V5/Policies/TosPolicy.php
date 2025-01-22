<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Modules\V5\Models\Tos;
use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Modules\V5\Common\OwnerAccess;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;

class TosPolicy
{
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

    /**
     * @param User $user
     * @return bool
     */
    public function index(User $user):bool
    {
        $empty_tos = new Tos();
        return $this->isAllowed($empty_tos, 'search', $user);
    }

    /**
     * @param User $user
     * @param Tos $tos
     * @return bool
     */
    public function show(User $user, Tos $tos):bool
    {
        return $this->isAllowed($tos, 'read', $user);
    }

    /**
     * @param User $user
     * @param Tos $tos
     * @return bool
     */
    public function delete(User $user, Tos $tos):bool
    {
        return $this->isAllowed($tos, 'delete', $user);
    }
    /**
     * @param User $user
     * @param Tos $tos
     * @return bool
     */
    public function update(User $user, Tos $tos):bool
    {
        return $this->isAllowed($tos, 'update', $user);
    }


    /**
     * @param User $user
     * @param Tos $tos
     * @return bool
     */
    public function store(User $user):bool
    {
        $tos = new Tos();
        return $this->isAllowed($tos, 'create', $user);
    }

    /**
     * @param Tos $tos
     * @param string $privilege
     * @param user $user
     * @return bool
     */
    public function isAllowed($tos, $privilege, $user = null):bool
    {
        //if user is not actual user, but is in fact anonymous
        if (($privilege === 'search' || $privilege === 'create')
            && $this->isUserAndOwnerAnonymous($tos, $user)) {
            return false;
        }

        // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        if ($user->id and $privilege === 'create') {
            return true;
        }

        if ($user->id and $privilege === 'search') {
            return true;
        }

        if ($privilege === 'read' && $tos->user_id === $user->id) {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }
}
