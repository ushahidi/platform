<?php

namespace Ushahidi\Modules\V5\Actions\Role\Commands;

use App\Bus\Command\Command;

class CreateRolePermissionCommand implements Command
{
    /**
     * @var string
     */
    private $role;

    /**
     * @var string
     */
    private $permission;
   

    public function __construct(string $role, string $permission)
    {
        $this->setRole($role);
        $this->setPermission($permission);
    }


    

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

     /**
     * @return void
     */
    public function setRole(string $role): void
    {
         $this->role = $role;
    }

    /**
     * @return string
     */
    public function getPermission(): string
    {
        return $this->permission;
    }

     /**
     * @return void
     */
    public function setPermission(string $permission): void
    {
         $this->permission = $permission;
    }
}
