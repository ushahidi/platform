<?php

namespace Ushahidi\Modules\V5\Actions\Post\Handlers;

use App\Bus\Action;
use App\Bus\Query\Query;
use App\Bus\Query\AbstractQueryHandler;
use Ushahidi\Modules\V5\Actions\Post\Queries\FetchPostLockByPostIdQuery;
use Ushahidi\Modules\V5\Repository\Post\PostLockRepository;

class FetchPostLockByPostIdQueryHandler extends AbstractQueryHandler
{
    private $post_lock_repository;
    public function __construct(PostLockRepository $post_lock_repository)
    {
        $this->post_lock_repository = $post_lock_repository;
    }

    protected function isSupported(Query $query)
    {
        if (!$query instanceof FetchPostLockByPostIdQuery) {
            throw new \InvalidArgumentException('Provided action is not supported');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var FetchPostLockByPostIdQuery $action
         */
         // To do : make it as transaction
        $this->isSupported($action);
        return $this->post_lock_repository->findByPostId($action->getPostId());
    }
}
