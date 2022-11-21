<?php

namespace Ushahidi\Modules\V5\Actions\User\Commands;

use App\Bus\Command\Command;
use Ushahidi\Core\Entity\User as UserEntity;

class UpdateUserCommand implements Command
{
    /**
     * @var UserEntity
     */
    private $entity;

    /**
     * @var int
     */
    private $id;
   

    public function __construct(int $id, UserEntity $entity)
    {
        $this->entity = $entity ;
        $this->id = $id;
    }


    /**
     * @return UserEntity
     */
    public function getEntity(): UserEntity
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
