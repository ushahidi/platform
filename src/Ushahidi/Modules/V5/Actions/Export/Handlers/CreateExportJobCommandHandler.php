<?php

namespace Ushahidi\Modules\V5\Actions\Export\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Export\Commands\CreateExportJobCommand;
use Ushahidi\Modules\V5\Repository\Export\ExportJobRepository;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\ExportJob;

class CreateExportJobCommandHandler extends AbstractCommandHandler
{
    private $export_job_repository;

    public function __construct(ExportJobRepository $export_job_repository)
    {
        $this->export_job_repository = $export_job_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof CreateExportJobCommand) {
            throw new \Exception('Provided $command is not instance of CreateExportJobCommand');
        }
    }

    /**
     * @param CreateExportJobCommand|Action $action
     * @return int Identifier of newly created record in the database.
     */
    public function __invoke(Action $action)
    {
        $this->isSupported($action);
        return $this->export_job_repository->create($action->getExportJobEntity());
    }
}
