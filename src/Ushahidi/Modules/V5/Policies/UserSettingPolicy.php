<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Modules\V5\Models\UserSetting;
use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\Acl as AccessControlList;

class UserSettingPolicy
{


    use UserContext;

    // It uses `PrivAccess` to provide the `getAllowedPrivs` method.
    use PrivAccess;

    // Check if user has Admin access
    use AdminAccess;

    // It uses `PrivateDeployment` to check whether a deployment is private
    use PrivateDeployment;

    // Check that the user has the necessary permissions
    use AccessControlList;

    use OwnerAccess;


    protected $user;

    /**
     * @param User $user
     * @return bool
     */
    public function index(User $user): bool
    {
        $empty_user_setting = new UserSetting();
        return $this->isAllowed($empty_user_setting, 'search', $user);
    }

    /**
     * @param User $user
     * @param UserSetting $user_setting
     * @return bool
     */
    public function show(User $user, UserSetting $user_setting): bool
    {
        return $this->isAllowed($user_setting, 'read', $user);
    }

    /**
     * @param User $user
     * @param UserSetting $user_setting
     * @return bool
     */
    public function delete(User $user, UserSetting $user_setting): bool
    {
        return $this->isAllowed($user_setting, 'delete', $user);
    }
    /**
     * @param User $user
     * @param UserSetting $user_setting
     * @return bool
     */
    public function update(User $user, UserSetting $user_setting): bool
    {
        return $this->isAllowed($user_setting, 'update', $user);
    }


    /**
     * @param User $user
     * @param UserSetting $user_setting
     * @return bool
     */
    public function store(User $user): bool
    {
        $user_setting = new UserSetting();
        return $this->isAllowed($user_setting, 'create', $user);
    }

    /**
     * @param UserSetting $user_setting
     * @param string $privilege
     * @param user $user
     * @return bool
     */
    public function isAllowed($user_setting, $privilege, $userModle = null): bool
    {

        $authorizer = service('authorizer.user_setting');
        $user = $authorizer->getUser();

        // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        // Regular user should be able to perform all actions on their own settings
        //  if ($this->isUserOwner($userModle, $user)) {
        return true;
        // }

        // Anyone can search, this is highly problematic because the results
        // are loaded and then filtered out based on the read priv
        if ($privilege === 'search') {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }
}
