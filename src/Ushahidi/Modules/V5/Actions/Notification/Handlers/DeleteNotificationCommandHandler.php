<?php

namespace Ushahidi\Modules\V5\Actions\Notification\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use App\Bus\Command\CommandHandler;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use Ushahidi\Modules\V5\Actions\Notification\Commands\DeleteNotificationCommand;
use Ushahidi\Modules\V5\Repository\Notification\NotificationRepository;

class DeleteNotificationCommandHandler extends V5CommandHandler
{
    private $notification_repository;
    public function __construct(NotificationRepository $notification_repository)
    {
        $this->notification_repository = $notification_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof DeleteNotificationCommand) {
            throw new \Exception('Provided command is not of type ' . DeleteNotificationCommand::class);
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var DeleteNotificationCommand $action
         */
        $this->isSupported($action);
        $this->notification_repository->delete($action->getId());
    }
}
