<?php

namespace Ushahidi\Modules\V5\Actions\Message\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Message\Commands\CreateMessageCommand;
use Ushahidi\Modules\V5\Repository\Message\MessageRepository;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\Message;

class CreateMessageCommandHandler extends AbstractCommandHandler
{
    private $message_repository;

    public function __construct(MessageRepository $message_repository)
    {
        $this->message_repository = $message_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof CreateMessageCommand) {
            throw new \Exception('Provided $command is not instance of CreateMessageCommand');
        }
    }

    /**
     * @param CreateMessageCommand|Action $action
     * @return int Identifier of newly created record in the database.
     */
    public function __invoke(Action $action)
    {
        $this->isSupported($action);
        return $this->message_repository->create($action->getMessageEntity());
    }
}
