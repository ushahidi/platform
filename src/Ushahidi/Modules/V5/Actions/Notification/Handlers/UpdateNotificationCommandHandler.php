<?php

namespace Ushahidi\Modules\V5\Actions\Notification\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ushahidi\Modules\V5\Models\Notification\Notification;
use Ushahidi\Modules\V5\Repository\Notification\NotificationRepository;
use Ushahidi\Modules\V5\Actions\Notification\Commands\UpdateNotificationCommand;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\NotificationLock as Lock;

class UpdateNotificationCommandHandler extends AbstractCommandHandler
{
    private $notification_repository;

    public function __construct(NotificationRepository $notification_repository)
    {
        $this->notification_repository = $notification_repository;
    }

    protected function isSupported(Command $command): void
    {
        if (!$command instanceof UpdateNotificationCommand) {
            throw new \Exception('Provided $command is not instance of UpdateNotificationCommand');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var UpdateNotificationCommand $action
         */
        $this->isSupported($action);

        return $this->notification_repository->update($action->getId(), $action->getNotificationEntity());
    }
}
