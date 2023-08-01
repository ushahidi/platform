<?php

namespace Ushahidi\Modules\V5\Actions\Apikey\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ushahidi\Modules\V5\Models\Apikey\Apikey;
use Ushahidi\Modules\V5\Repository\Apikey\ApikeyRepository;
use Ushahidi\Modules\V5\Actions\Apikey\Commands\UpdateApikeyCommand;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\ApikeyLock as Lock;

class UpdateApikeyCommandHandler extends AbstractCommandHandler
{
    private $apikey_repository;

    public function __construct(ApikeyRepository $apikey_repository)
    {
        $this->apikey_repository = $apikey_repository;
    }

    protected function isSupported(Command $command): void
    {
        if (!$command instanceof UpdateApikeyCommand) {
            throw new \Exception('Provided $command is not instance of UpdateApikeyCommand');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var UpdateApikeyCommand $action
         */
        $this->isSupported($action);

        return $this->apikey_repository->update($action->getId(), $action->getApikeyEntity());
    }
}
