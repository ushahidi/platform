<?php

namespace Ushahidi\Modules\V5\Actions\Media\Commands;

use App\Bus\Command\Command;

class DeleteMediaCommand implements Command
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
