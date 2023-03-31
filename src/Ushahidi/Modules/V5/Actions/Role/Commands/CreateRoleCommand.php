<?php

namespace Ushahidi\Modules\V5\Actions\Role\Commands;

use App\Bus\Command\Command;
use Ushahidi\Core\Entity\Role as RoleEntity;

class CreateRoleCommand implements Command
{
    /**
     * @var RoleEntity
     */
    private $entity;

    /**
     * @var int
     */
    private $id;

    /**
     * @var array
     */
    private $permissions;

    public function __construct(RoleEntity $entity, array $permissions = [])
    {
        $this->entity = $entity;
        $this->permissions = $permissions;
    }

    /**
     * @return RoleEntity
     */
    public function getEntity(): RoleEntity
    {
        return $this->entity;
    }

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }



    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
