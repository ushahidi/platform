<?php

namespace Ushahidi\Modules\V5\Actions\Export\Commands;

use App\Bus\Command\Command;

class DeleteExportJobCommand implements Command
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
