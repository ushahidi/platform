<?php

namespace Ushahidi\Modules\V5\Actions\Role\Commands;

use App\Bus\Command\Command;

class DeleteRolePermissionByRoleCommand implements Command
{


    /**
     * @var string
     */
    private $role;

    public function __construct(string $role)
    {
        $this->role = $role;
    }


    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }
}
