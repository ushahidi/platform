<?php

namespace Ushahidi\Modules\V5\Actions\CSV\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\CSV\Commands\CreateCSVCommand;
use Ushahidi\Modules\V5\Repository\CSV\CSVRepository;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\CSV;

class CreateCSVCommandHandler extends AbstractCommandHandler
{
    private $csv_repository;

    public function __construct(CSVRepository $csv_repository)
    {
        $this->csv_repository = $csv_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof CreateCSVCommand) {
            throw new \Exception('Provided $command is not instance of CreateCSVCommand');
        }
    }

    /**
     * @param CreateCSVCommand|Action $action
     * @return int Identifier of newly created record in the database.
     */
    public function __invoke(Action $action)
    {
        $this->isSupported($action);
        return $this->csv_repository->create($action->getCSVEntity());
    }
}
