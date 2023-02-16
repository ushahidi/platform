<?php

namespace Ushahidi\Modules\V5\Actions\Collection\Handlers;

use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Collection\Commands\DeleteCollectionCommand;
use Ushahidi\Modules\V5\Repository\Set\SetRepository as CollectionRepository;

class DeleteCollectionCommandHandler extends AbstractCommandHandler
{

    private $collection_repository;

    public function __construct(CollectionRepository $collection_repository)
    {
        $this->collection_repository = $collection_repository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === DeleteCollectionCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param DeleteCollectionCommand $command
     * @return int
     */
    public function __invoke($command) //: int
    {
        $this->isSupported($command);
        $this->collection_repository->delete($command->getId());
    }
}
