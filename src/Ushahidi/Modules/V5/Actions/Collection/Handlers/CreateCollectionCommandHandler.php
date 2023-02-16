<?php

namespace Ushahidi\Modules\V5\Actions\Collection\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Collection\Commands\CreateCollectionCommand;
use Ushahidi\Modules\V5\Repository\Set\SetRepository as CollectionRepository;

class CreateCollectionCommandHandler extends AbstractCommandHandler
{

    private $collection_repository;

    public function __construct(CollectionRepository $collection_repository)
    {
        $this->collection_repository = $collection_repository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === CreateCollectionCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param CreateCollectionCommand $command
     * @return int
     */
    public function __invoke($command) //: int

    {
        $this->isSupported($command);
        return $this->collection_repository->create($command->getEntity());
    }
}
