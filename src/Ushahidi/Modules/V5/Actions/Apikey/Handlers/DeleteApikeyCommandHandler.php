<?php

namespace Ushahidi\Modules\V5\Actions\Apikey\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use App\Bus\Command\CommandHandler;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use Ushahidi\Modules\V5\Actions\Apikey\Commands\DeleteApikeyCommand;
use Ushahidi\Modules\V5\Repository\Apikey\ApikeyRepository;

class DeleteApikeyCommandHandler extends V5CommandHandler
{
    private $apikey_repository;
    public function __construct(ApikeyRepository $apikey_repository)
    {
        $this->apikey_repository = $apikey_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof DeleteApikeyCommand) {
            throw new \Exception('Provided command is not of type ' . DeleteApikeyCommand::class);
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var DeleteApikeyCommand $action
         */
        $this->isSupported($action);
        $this->apikey_repository->delete($action->getId());
    }
}
