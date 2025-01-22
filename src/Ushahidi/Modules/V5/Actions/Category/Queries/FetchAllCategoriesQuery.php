<?php

namespace Ushahidi\Modules\V5\Actions\Category\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\CategorySearchFields;
use Ushahidi\Modules\V5\Models\Category;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Traits\OnlyParameter\QueryWithOnlyParameter;
use Ushahidi\Modules\V5\Traits\HasPaginate;
use Ushahidi\Modules\V5\Traits\HasSearchFields;

class FetchAllCategoriesQuery implements Query
{

    use QueryWithOnlyParameter;
    use HasPaginate;
    use HasSearchFields;
   
    public static function fromRequest(Request $request): self
    {
        $query = new self();
        $query->setDefaultLimit(1000);
        $query->setPaging($request);
        $query->setSearchFields(new CategorySearchFields($request));
        $query->addOnlyParameteresFromRequest(
            $request,
            Category::ALLOWED_FIELDS,
            Category::ALLOWED_RELATIONSHIPS,
            Category::REQUIRED_FIELDS
        );
        return $query;
    }
}
