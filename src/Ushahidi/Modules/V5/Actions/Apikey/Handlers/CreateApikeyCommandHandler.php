<?php

namespace Ushahidi\Modules\V5\Actions\Apikey\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Apikey\Commands\CreateApikeyCommand;
use Ushahidi\Modules\V5\Repository\Apikey\ApikeyRepository;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\Apikey;

class CreateApikeyCommandHandler extends AbstractCommandHandler
{
    private $apikey_repository;

    public function __construct(ApikeyRepository $apikey_repository)
    {
        $this->apikey_repository = $apikey_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof CreateApikeyCommand) {
            throw new \Exception('Provided $command is not instance of CreateApikeyCommand');
        }
    }

    /**
     * @param CreateApikeyCommand|Action $action
     * @return int Identifier of newly created record in the database.
     */
    public function __invoke(Action $action)
    {
        $this->isSupported($action);
        return $this->apikey_repository->create($action->getApikeyEntity());
    }
}
