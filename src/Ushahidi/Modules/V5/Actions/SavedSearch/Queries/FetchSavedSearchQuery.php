<?php

namespace Ushahidi\Modules\V5\Actions\SavedSearch\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\DTO\SavedSearchSearchFields;
use Ushahidi\Modules\V5\Traits\OnlyParameter\QueryWithOnlyParameter;
use Ushahidi\Modules\V5\Traits\HasPaginate;
use Ushahidi\Modules\V5\Traits\HasSearchFields;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Models\Set;

class FetchSavedSearchQuery implements Query
{
    use QueryWithOnlyParameter;
    use HasPaginate;
    use HasSearchFields;

    const DEFAULT_LIMIT = 0;
    const DEFAULT_ORDER = "ASC";
    const DEFAULT_SORT_BY = "id";

    public static function fromRequest(Request $request): self
    {
        $query = new self();
        $query->setPaging($request, self::DEFAULT_SORT_BY, self::DEFAULT_ORDER, self::DEFAULT_LIMIT);
        $query->setSearchFields(new SavedSearchSearchFields($request));
        $query->addOnlyParameteresFromRequest($request, Set::ALLOWED_FIELDS, Set::ALLOWED_RELATIONSHIPS, Set::REQUIRED_FIELDS);
        return $query;
    }
}
