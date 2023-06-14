<?php

namespace Ushahidi\Modules\V5\Actions\Layer\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ushahidi\Modules\V5\Models\Layer\Layer;
use Ushahidi\Modules\V5\Repository\Layer\LayerRepository;
use Ushahidi\Modules\V5\Actions\Layer\Commands\UpdateLayerCommand;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\LayerLock as Lock;

class UpdateLayerCommandHandler extends AbstractCommandHandler
{
    private $layer_repository;

    public function __construct(LayerRepository $layer_repository)
    {
        $this->layer_repository = $layer_repository;
    }

    protected function isSupported(Command $command): void
    {
        if (!$command instanceof UpdateLayerCommand) {
            throw new \Exception('Provided $command is not instance of UpdateLayerCommand');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var UpdateLayerCommand $action
         */
        $this->isSupported($action);

        return $this->layer_repository->update($action->getId(), $action->getLayerEntity());
    }
}
