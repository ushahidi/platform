<?php

namespace Ushahidi\Modules\V5\Actions\Config\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ushahidi\Modules\V5\Models\Config;
use Ushahidi\Modules\V5\Repository\Config\ConfigRepository;
use Ushahidi\Modules\V5\Actions\Config\Commands\UpdateConfigCommand;
use Illuminate\Support\Facades\DB;

class UpdateConfigCommandHandler extends AbstractCommandHandler
{
    private $config_repository;

    public function __construct(ConfigRepository $config_repository)
    {
        $this->config_repository = $config_repository;
    }

    protected function isSupported(Command $command): void
    {
        if (!$command instanceof UpdatePUpdateConfigCommandostCommand) {
            throw new \Exception('Provided $command is not instance of UpdateConfigCommand');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var UpdateConfigCommand $action
         */
        $this->isSupported($action);

     
         $this->updateConfig($action);
    }


    private function updateConfig(UpdateConfigCommand $action)
    {
    }
}
