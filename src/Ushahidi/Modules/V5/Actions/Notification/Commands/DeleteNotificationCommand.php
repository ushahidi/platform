<?php

namespace Ushahidi\Modules\V5\Actions\Notification\Commands;

use App\Bus\Command\Command;

class DeleteNotificationCommand implements Command
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
