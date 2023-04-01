<?php

namespace Ushahidi\Modules\V5\Actions\Post\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use App\Bus\Action;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Ushahidi\Modules\V5\Actions\Post\Queries\ListPostsQuery;
use Ushahidi\Modules\V5\Repository\Post\PostRepository;

class ListPostsQueryHandler extends AbstractQueryHandler
{
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    protected function isSupported(Query $query)
    {
        if (!$query instanceof ListPostsQuery) {
            throw new \InvalidArgumentException('Provided action is not supported');
        }
    }

    public function __invoke(Action $action): LengthAwarePaginator
    {
        /**
         * @var ListPostsQuery $action
         */
        $this->isSupported($action);

        return $this->postRepository
            ->paginate(
                $action->getLimit(),
                $action->getFields()
            );
    }
}
