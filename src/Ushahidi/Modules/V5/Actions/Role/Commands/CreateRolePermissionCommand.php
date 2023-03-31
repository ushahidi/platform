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
        $this->role = $role;
        $this->permission = $permission;
    }


    

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }
  
    /**
     * @return string
     */
    public function getPermission(): string
    {
        return $this->permission;
    }
}
