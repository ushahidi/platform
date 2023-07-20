<?php

namespace Ushahidi\Modules\V5\Actions\CSV\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use App\Bus\Command\CommandHandler;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use Ushahidi\Modules\V5\Actions\CSV\Commands\DeleteCSVCommand;
use Ushahidi\Modules\V5\Repository\CSV\CSVRepository;

class DeleteCSVCommandHandler extends V5CommandHandler
{
    private $csv_repository;
    public function __construct(CSVRepository $csv_repository)
    {
        $this->csv_repository = $csv_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof DeleteCSVCommand) {
            throw new \Exception('Provided command is not of type ' . DeleteCSVCommand::class);
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var DeleteCSVCommand $action
         */
        $this->isSupported($action);
        $this->csv_repository->delete($action->getId());
    }
}
