<?php

namespace Ushahidi\Modules\V5\Actions\Export\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ushahidi\Modules\V5\Models\ExportJob;
use Ushahidi\Modules\V5\Repository\Export\ExportJobRepository;
use Ushahidi\Modules\V5\Actions\Export\Commands\UpdateExportJobCommand;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\ExportJobLock as Lock;

class UpdateExportJobCommandHandler extends AbstractCommandHandler
{
    private $export_job_repository;

    public function __construct(ExportJobRepository $export_job_repository)
    {
        $this->export_job_repository = $export_job_repository;
    }

    protected function isSupported(Command $command): void
    {
        if (!$command instanceof UpdateExportJobCommand) {
            throw new \Exception('Provided $command is not instance of UpdateExportJobCommand');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var UpdateExportJobCommand $action
         */
        $this->isSupported($action);

        return $this->export_job_repository->update($action->getId(), $action->getExportJobEntity());
    }
}
