<?php

namespace Ushahidi\Modules\V3\Policies;

use App\Auth\GenericUser as User;
use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Tool\Acl;
use Ushahidi\Modules\V5\Repository\Role\RoleRepository;

class CountryCodePolicy
{
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function view(User $user): bool
    {
        return $this->isAllowedToRead($user);
    }

    public function show(User $user): bool
    {
        return $this->isAllowedToRead($user);
    }

    private function isAllowedToRead(User $user): bool
    {
        if (!$user->role) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        $role = $this->roleRepository->findByRole($user->role);

        $permissions = $role->getPermission();

        if ($permissions->isEmpty()) {
            return false;
        }

        return $permissions->contains(Permission::MANAGE_SETTINGS);
    }
}
