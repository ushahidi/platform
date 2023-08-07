<?php

namespace Ushahidi\Modules\V5\Actions\HXL\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Contact\Commands\CreateHXLCommand;
use Ushahidi\Modules\V5\Repository\HXL\HXLRepository;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\HXL;

class CreateHXLMetaDataCommandHandler extends AbstractCommandHandler
{
    private $hxl_repository;

    public function __construct(HXLRepository $hxl_repository)
    {
        $this->hxl_repository = $hxl_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof CreateHXLCommand) {
            throw new \Exception('Provided $command is not instance of CreateHXLCommand');
        }
    }

    /**
     * @param CreateHXLCommand|Action $action
     * @return int Identifier of newly created record in the database.
     */
    public function __invoke(Action $action)
    {
        $this->isSupported($action);
        return $this->hxl_repository->create($action->getHXLEntity());
    }
}
