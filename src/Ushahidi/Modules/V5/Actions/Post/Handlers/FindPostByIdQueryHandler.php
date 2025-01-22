<?php

namespace Ushahidi\Modules\V5\Actions\Post\Handlers;

use App\Bus\Action;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Post\Handlers\AbstractPostQueryHandler;
use Ushahidi\Modules\V5\Actions\Post\Queries\FindPostByIdQuery;
use Ushahidi\Modules\V5\Repository\Post\PostRepository;
use Ushahidi\Modules\V5\Actions\Post\HandlePostOnlyParameters;

class FindPostByIdQueryHandler extends AbstractPostQueryHandler
{
    use HandlePostOnlyParameters;
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    protected function isSupported(Query $query)
    {
        if (!$query instanceof FindPostByIdQuery) {
            throw new \InvalidArgumentException('Provided action is not supported');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var FindPostByIdQuery $action
         */
        $this->isSupported($action);

        $post = $this->postRepository->findById(
            $action->getId(),
            $this->updateSelectFieldsDependsOnPermissions(
                array_unique(array_merge($action->getFields(), $action->getFieldsForRelationship()))
            ),
            $action->getWithRelationship()
        );
        $post = $this->addHydrateRelationships($post, $action->getHydrates());
        $post = $this->hideFieldsUsedByRelationships(
            $post,
            array_diff($action->getFieldsForRelationship(), $action->getFields())
        );
        $post = $this->handleSourceField($post);
        return $this->hideUnwantedRelationships($post, $action->getHydrates());
    }
}
