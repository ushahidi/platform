<?php

namespace Ushahidi\Modules\V5\Actions\Contact\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use App\Bus\Command\CommandHandler;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use Ushahidi\Modules\V5\Actions\Contact\Commands\DeleteContactCommand;
use Ushahidi\Modules\V5\Repository\Contact\ContactRepository;

class DeleteContactCommandHandler extends V5CommandHandler
{
    private $contact_repository;
    public function __construct(ContactRepository $contact_repository)
    {
        $this->contact_repository = $contact_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof DeleteContactCommand) {
            throw new \Exception('Provided command is not of type ' . DeleteContactCommand::class);
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var DeleteContactCommand $action
         */
        $this->isSupported($action);
        $this->contact_repository->delete($action->getId());
    }
}
