<?php

namespace Ushahidi\Modules\V5\Actions\Collection\Commands;

use App\Bus\Command\Command;

class DeleteCollectionCommand implements Command
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
