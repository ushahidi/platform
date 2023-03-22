<?php

namespace Ushahidi\Modules\V5\Actions\Role\Commands;

use App\Bus\Command\Command;
use Ushahidi\Core\Entity\role as RoleEntity;

class UpdateRoleCommand implements Command
{
    /**
     * @var RoleEntity
     */
    private $entity;

    /**
     * @var int
     */
    private $id;
   

    public function __construct(int $id, RoleEntity $entity)
    {
        $this->entity = $entity;
        $this->id = $id;
    }


   /**
     * @return RoleEntity
     */
    public function getEntity(): RoleEntity
    {
        return $this->entity;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
