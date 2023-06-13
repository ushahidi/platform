<?php

namespace Ushahidi\Modules\V5\Actions\Notification\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Notification\Commands\CreateNotificationCommand;
use Ushahidi\Modules\V5\Repository\Notification\NotificationRepository;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\Notification;

class CreateNotificationCommandHandler extends AbstractCommandHandler
{
    private $notification_repository;

    public function __construct(NotificationRepository $notification_repository)
    {
        $this->notification_repository = $notification_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof CreateNotificationCommand) {
            throw new \Exception('Provided $command is not instance of CreateNotificationCommand');
        }
    }

    /**
     * @param CreateNotificationCommand|Action $action
     * @return int Identifier of newly created record in the database.
     */
    public function __invoke(Action $action)
    {
        $this->isSupported($action);
        return $this->notification_repository->create($action->getNotificationEntity());
    }
}
