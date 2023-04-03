<?php

namespace Ushahidi\Modules\V5\Actions\Category\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\CategorySearchFields;

class FetchAllCategoriesQuery implements Query
{

    /**
     * @var Paging
     */
    private $paging;
    private $category_search_fields;

    public function __construct(Paging $paging, CategorySearchFields $category_search_fields)
    {
        $this->paging = $paging;
        $this->category_search_fields = $category_search_fields;
    }

    public function getPaging(): Paging
    {
        return $this->paging;
    }

    public function getCategorySearchFields()
    {
        return $this->category_search_fields;
    }
}
