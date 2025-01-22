<?php

namespace Ushahidi\Modules\V5\Actions\Collection\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\DTO\CollectionSearchFields;
use Ushahidi\Modules\V5\Traits\OnlyParameter\QueryWithOnlyParameter;
use Ushahidi\Modules\V5\Traits\HasPaginate;
use Ushahidi\Modules\V5\Traits\HasSearchFields;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Models\Set as CollectionModel;

class FetchCollectionQuery implements Query
{
    use QueryWithOnlyParameter;
    use HasPaginate;
    use HasSearchFields;

    const DEFAULT_LIMIT = 0;
    const DEFAULT_ORDER = "DESC";
    const DEFAULT_SORT_BY = "featured";

    public static function fromRequest(Request $request): self
    {
        $query = new self();
        $query->setPaging($request, self::DEFAULT_SORT_BY, self::DEFAULT_ORDER, self::DEFAULT_LIMIT);
        $query->setSearchFields(new CollectionSearchFields($request));
        $query->addOnlyParameteresFromRequest(
            $request,
            CollectionModel::COLLECTION_ALLOWED_FIELDS,
            CollectionModel::ALLOWED_RELATIONSHIPS,
            CollectionModel::REQUIRED_FIELDS
        );
        return $query;
    }
}
