<?php

namespace Ushahidi\Modules\V5\Actions\Export\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use App\Bus\Command\CommandHandler;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use Ushahidi\Modules\V5\Actions\Export\Commands\DeleteExportJobCommand;
use Ushahidi\Modules\V5\Repository\Export\ExportJobRepository;

class DeleteExportJobCommandHandler extends V5CommandHandler
{
    private $export_job_repository;
    public function __construct(ExportJobRepository $export_job_repository)
    {
        $this->export_job_repository = $export_job_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof DeleteExportJobCommand) {
            throw new \Exception('Provided command is not of type ' . DeleteExportJobCommand::class);
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var DeleteExportJobCommand $action
         */
        $this->isSupported($action);
        $this->export_job_repository->delete($action->getId());
    }
}
