<?php

namespace Ushahidi\Modules\V5\Actions\CSV\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ushahidi\Modules\V5\Models\CSV\CSV;
use Ushahidi\Modules\V5\Repository\CSV\CSVRepository;
use Ushahidi\Modules\V5\Actions\CSV\Commands\UpdateCSVCommand;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\CSVLock as Lock;

class UpdateCSVCommandHandler extends AbstractCommandHandler
{
    private $csv_repository;

    public function __construct(CSVRepository $csv_repository)
    {
        $this->csv_repository = $csv_repository;
    }

    protected function isSupported(Command $command): void
    {
        if (!$command instanceof UpdateCSVCommand) {
            throw new \Exception('Provided $command is not instance of UpdateCSVCommand');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var UpdateCSVCommand $action
         */
        $this->isSupported($action);

        return $this->csv_repository->update($action->getId(), $action->getCSVEntity());
    }
}
