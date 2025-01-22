<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\DTO\SurveySearchFields;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Models\Survey;
use Ushahidi\Modules\V5\Traits\OnlyParameter\QueryWithOnlyParameter;
use Ushahidi\Modules\V5\Traits\HasPaginate;
use Ushahidi\Modules\V5\Traits\HasSearchFields;

class FetchSurveyQuery implements Query
{
    use QueryWithOnlyParameter;
    use HasPaginate;
    use HasSearchFields;

    const DEFAULT_LIMIT = 10000;
    const DEFAULT_ORDER = "ASC";
    const DEFAULT_SORT_BY = "id";

    public static function fromRequest(Request $request): self
    {
        $query = new self();
        $query->setPaging($request, self::DEFAULT_SORT_BY, self::DEFAULT_ORDER, self::DEFAULT_LIMIT);
        $query->setSearchFields(new SurveySearchFields($request));
        $query->addOnlyParameteresFromRequest($request, Survey::ALLOWED_FIELDS, Survey::ALLOWED_RELATIONSHIPS, Survey::REQUIRED_FIELDS);
        return $query;
    }
}
