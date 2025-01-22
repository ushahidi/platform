<?php

namespace Ushahidi\Modules\V5\Actions\Post\Handlers;

use Ushahidi\Modules\V5\Actions\Post\Handlers\AbstractPostQueryHandler;
use App\Bus\Query\Query;
use App\Bus\Action;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Ushahidi\Modules\V5\Actions\Post\Queries\ListPostsQuery;
use Ushahidi\Modules\V5\Repository\Post\PostRepository;
use Ushahidi\Modules\V5\Actions\Post\HandlePostOnlyParameters;

class ListPostsQueryHandler extends AbstractPostQueryHandler
{
    use HandlePostOnlyParameters;
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

        $posts = $this->postRepository
            ->paginate(
                $action->getPaging(),
                $action->getSearchFields(),
                $this->updateSelectFieldsDependsOnPermissions(
                    array_unique(array_merge($action->getFields(), $action->getFieldsForRelationship()))
                ),
                $action->getWithRelationship()
            );
            $result = [];
        foreach ($posts as &$post) {
            $post = $this->addHydrateRelationships($post, $action->getHydrates());
            $post = $this->hideFieldsUsedByRelationships(
                $post,
                array_diff($action->getFieldsForRelationship(), $action->getFields())
            );
            $post = $this->hideUnwantedRelationships($post, $action->getHydrates());
            $post = $this->handleSourceField($post);
            $result[] = $post;
        }
        return $posts;
    }
}
