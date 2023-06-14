<?php

namespace Ushahidi\Modules\V5\Actions\Layer\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Layer\Commands\CreateLayerCommand;
use Ushahidi\Modules\V5\Repository\Layer\LayerRepository;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\Layer;

class CreateLayerCommandHandler extends AbstractCommandHandler
{
    private $layer_repository;

    public function __construct(LayerRepository $layer_repository)
    {
        $this->layer_repository = $layer_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof CreateLayerCommand) {
            throw new \Exception('Provided $command is not instance of CreateLayerCommand');
        }
    }

    /**
     * @param CreateLayerCommand|Action $action
     * @return int Identifier of newly created record in the database.
     */
    public function __invoke(Action $action)
    {
        $this->isSupported($action);
        return $this->layer_repository->create($action->getLayerEntity());
    }
}
