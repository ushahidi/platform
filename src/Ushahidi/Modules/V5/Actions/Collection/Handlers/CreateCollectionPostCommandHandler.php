<?php

namespace Ushahidi\Modules\V5\Actions\Collection\Handlers;

use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Collection\Commands\CreateCollectionPostCommand;
use Ushahidi\Modules\V5\Repository\Set\SetPostRepository as CollectionPostRepository;

class CreateCollectionPostCommandHandler extends AbstractCommandHandler
{

    private $collection_post_repository;

    public function __construct(CollectionPostRepository $collection_post_repository)
    {
        $this->collection_post_repository = $collection_post_repository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === CreateCollectionPostCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param CreateCollectionPostCommand $command
     * @return int
     */
    public function __invoke($action) //: int
    {
        $this->isSupported($action);
        $this->collection_post_repository->create($action->getCollectionId(), $action->getPostId());
    }
}
