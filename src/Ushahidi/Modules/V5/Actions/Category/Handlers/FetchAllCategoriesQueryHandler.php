<?php

namespace Ushahidi\Modules\V5\Actions\Category\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Core\Tool\SearchData;
use Ushahidi\Modules\V5\Actions\Category\Queries\FetchAllCategoriesQuery;
use Ushahidi\Modules\V5\Repository\Category\CategoryRepository;
use Illuminate\Support\Facades\Auth;

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

    public function __invoke(Action $action)
    {
        /**
         * @var FetchAllCategoriesQuery $action
         */
        $this->isSupported($action);

        $data = new SearchData;

        $searchFields = $action->getCategorySearchFields();

        $user = Auth::guard()->user();

        $data->setFilter('keyword', $searchFields->q());

        $data->setFilter('tag', $searchFields->tag());
        $data->setFilter('type', $searchFields->type());
        $data->setFilter('role', $searchFields->role());
        $data->setFilter('user_id', $user->id ?? null);
        $data->setFilter('parent_id', $searchFields->parentId());
        $data->setFilter('is_parent', $searchFields->level() === 'parent');
        $data->setFilter('is_admin', $searchFields->role() && $searchFields->role() == "admin");

        $this->categoryRepository->setSearchParams($data);
        return $this->categoryRepository->fetchAll($action->getPaging());
    }
}
