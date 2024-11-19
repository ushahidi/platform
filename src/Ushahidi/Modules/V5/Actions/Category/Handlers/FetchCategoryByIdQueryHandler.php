<?php


namespace Ushahidi\Modules\V5\Actions\Category\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Category\Queries\FetchCategoryByIdQuery;
use Ushahidi\Modules\V5\Models\Category;
use Ushahidi\Modules\V5\Repository\Category\CategoryRepository;

class FetchCategoryByIdQueryHandler extends AbstractQueryHandler
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    protected function isSupported(Query $query): void
    {
        if (!$query instanceof FetchCategoryByIdQuery) {
            throw new \Exception('Provided query is not a FetchCategoryByIdQuery');
        }
    }

    public function __invoke(Action $query): Category
    {
        /**
         * @var FetchCategoryByIdQuery $action
         */
        $this->isSupported($query);
        return $this->categoryRepository->findById(
            $query->getId(),
            false,
            array_unique(array_merge(
                $query->getFields(),
                $query->getFieldsForRelationship()
            )),
            $query->getWithRelationship()
        );
    }
}
