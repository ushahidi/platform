<?php

namespace Ushahidi\Modules\V5\Actions\Collection\Handlers;

use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Collection\Commands\DeleteCollectionPostCommand;
use Ushahidi\Modules\V5\Repository\Set\SetPostRepository as CollectionPostRepository;

class DeleteCollectionPostCommandHandler extends AbstractCommandHandler
{

    private $collection_post_repository;

    public function __construct(CollectionPostRepository $collection_post_repository)
    {
        $this->collection_post_repository = $collection_post_repository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === DeleteCollectionPostCommand::class,
            'Provided command not supported'
        );
    }

    /**
     * run the command handler
     * @param DeleteCollectionPostCommand $command
     * @return int
     */
    public function __invoke($action)
    {
        $this->isSupported($action);
        $this->collection_post_repository->delete($action->getCollectionId(), $action->getPostId());
    }
}
