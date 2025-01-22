<?php

namespace Ushahidi\Modules\V5\Actions\Message\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ushahidi\Modules\V5\Models\Message\Message;
use Ushahidi\Modules\V5\Repository\Message\MessageRepository;
use Ushahidi\Modules\V5\Actions\Message\Commands\UpdateMessageCommand;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\MessageLock as Lock;

class UpdateMessageCommandHandler extends AbstractCommandHandler
{
    private $message_repository;

    public function __construct(MessageRepository $message_repository)
    {
        $this->message_repository = $message_repository;
    }

    protected function isSupported(Command $command): void
    {
        if (!$command instanceof UpdateMessageCommand) {
            throw new \Exception('Provided $command is not instance of UpdateMessageCommand');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var UpdateMessageCommand $action
         */
        $this->isSupported($action);

        return $this->message_repository->update($action->getId(), $action->getMessageEntity());
    }
}
