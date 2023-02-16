<?php

namespace Ushahidi\Modules\V5\Actions\Collection\Handlers;

use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Collection\Commands\UpdateCollectionCommand;
use Ushahidi\Modules\V5\Repository\Set\SetRepository as CollectionRepository;

class UpdateCollectionCommandHandler extends AbstractCommandHandler
{

    private $collection_repository;

    public function __construct(CollectionRepository $collection_repository)
    {
        $this->collection_repository = $collection_repository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === UpdateCollectionCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param UpdateCollectionCommand $command
     * @return int
     */
    public function __invoke($command)
    {
        $this->isSupported($command);
        $this->collection_repository->update($command->getId(), $command->getEntity());
    }
}
