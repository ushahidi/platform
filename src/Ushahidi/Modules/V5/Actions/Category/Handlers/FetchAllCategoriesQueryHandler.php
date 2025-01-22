<?php

namespace Ushahidi\Modules\V5\Actions\Category\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Category\Queries\FetchAllCategoriesQuery;
use Ushahidi\Modules\V5\Repository\Category\CategoryRepository;

class FetchAllCategoriesQueryHandler extends AbstractQueryHandler
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
    protected function isSupported(Query $query)
    {
        if (!$query instanceof FetchAllCategoriesQuery) {
            throw new \Exception('Provided query is not a FetchAllCategoriesQuery');
        }
    }

    public function __invoke(Action $query)
    {
        /**
         * @var FetchAllCategoriesQuery $action
         */
        $this->isSupported($query);
        $only_fields =  array_unique(array_merge($query->getFields(), $query->getFieldsForRelationship()));
        return $this->categoryRepository->paginate(
            $query->getPaging(),
            $query->getSearchFields(),
            $only_fields,
            $query->getWithRelationship()
        );
    }
}
