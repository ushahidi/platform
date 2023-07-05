<?php

namespace Ushahidi\Modules\V5\Actions\Message\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use App\Bus\Command\CommandHandler;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use Ushahidi\Modules\V5\Actions\Message\Commands\DeleteMessageCommand;
use Ushahidi\Modules\V5\Repository\Message\MessageRepository;

class DeleteMessageCommandHandler extends V5CommandHandler
{
    private $message_repository;
    public function __construct(MessageRepository $message_repository)
    {
        $this->message_repository = $message_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof DeleteMessageCommand) {
            throw new \Exception('Provided command is not of type ' . DeleteMessageCommand::class);
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var DeleteMessageCommand $action
         */
        $this->isSupported($action);
        $this->message_repository->delete($action->getId());
    }
}
