<?php

namespace Ushahidi\Modules\V5\Actions\Post\Commands;

use App\Bus\Command\Command;

class DeletePostCommand implements Command
{
    /**
     * @var int
     */
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
