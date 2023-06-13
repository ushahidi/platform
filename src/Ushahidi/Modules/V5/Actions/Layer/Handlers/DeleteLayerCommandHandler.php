<?php

namespace Ushahidi\Modules\V5\Actions\Layer\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use App\Bus\Command\CommandHandler;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use Ushahidi\Modules\V5\Actions\Layer\Commands\DeleteLayerCommand;
use Ushahidi\Modules\V5\Repository\Layer\LayerRepository;

class DeleteLayerCommandHandler extends V5CommandHandler
{
    private $layer_repository;
    public function __construct(LayerRepository $layer_repository)
    {
        $this->layer_repository = $layer_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof DeleteLayerCommand) {
            throw new \Exception('Provided command is not of type ' . DeleteLayerCommand::class);
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var DeleteLayerCommand $action
         */
        $this->isSupported($action);
        $this->layer_repository->delete($action->getId());
    }
}
