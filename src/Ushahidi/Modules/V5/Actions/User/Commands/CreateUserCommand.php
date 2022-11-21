<?php

namespace Ushahidi\Modules\V5\Actions\User\Commands;

use App\Bus\Command\Command;
use Ushahidi\Core\Entity\User as UserEntity;

class CreateUserCommand implements Command
{
    /**
     * @var UserEntity
     */
    private $entity;

    /**
     * @var int
     */
    private $id;
   
    public function __construct(UserEntity $user_entity)
    {
        $this->entity = $user_entity;
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
        return  $this->id;
    }

     /**
     * @return void
     */
    public function setId(int $id): void
    {
         $this->id = $id;
    }
}
