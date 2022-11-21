<?php

namespace Ushahidi\Modules\V5\Actions\User\Commands;

use App\Bus\Command\Command;

class DeleteUserCommand implements Command
{


    /**
     * @var int
     */
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
