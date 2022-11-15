<?php

namespace Ushahidi\Modules\V5\Actions\Role\Commands;

use App\Bus\Command\Command;

class DeleteRoleCommand implements Command
{


    /**
     * @var int
     */
    private $id;

    public function __construct(int $id)
    {

        $this->setId($id);
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
