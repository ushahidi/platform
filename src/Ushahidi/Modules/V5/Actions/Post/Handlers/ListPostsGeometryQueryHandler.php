<?php

namespace Ushahidi\Modules\V5\Actions\Post\Handlers;

use App\Bus\Action;
use App\Bus\Query\Query;
use App\Bus\Query\AbstractQueryHandler;
use Ushahidi\Modules\V5\Actions\Post\Queries\ListPostsGeometryQuery;
use Ushahidi\Modules\V5\Repository\Post\PostRepository;
use Ushahidi\Core\Concerns\GeometryConverter;
use Symm\Gisconverter\Decoders\WKT;

class ListPostsGeometryQueryHandler extends AbstractQueryHandler
{
    use GeometryConverter;
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    protected function isSupported(Query $query)
    {
        if (!$query instanceof ListPostsGeometryQuery) {
            throw new \InvalidArgumentException('Provided action is not supported');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var ListPostsGeometryQuery $action
         */
        $this->isSupported($action);
         return $this->postRepository->getPostsGeoJson($action->getPaging(), $action->getSearchFields());
    }
}
