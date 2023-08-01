<?php

namespace Ushahidi\Modules\V5\Actions\Apikey\Commands;

use App\Bus\Command\Command;

class DeleteApikeyCommand implements Command
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
