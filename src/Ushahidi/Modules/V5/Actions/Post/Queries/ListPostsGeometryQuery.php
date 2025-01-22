<?php

namespace Ushahidi\Modules\V5\Actions\Post\Queries;

use App\Bus\Query\Query;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\DTO\PostSearchFields;
use Ushahidi\Modules\V5\Traits\HasPaginate;
use Ushahidi\Modules\V5\Traits\HasSearchFields;

class ListPostsGeometryQuery implements Query
{
    use HasPaginate;
    use HasSearchFields;

    public static function fromRequest(Request $request, array $surveys_with_private_location = []): self
    {
        $post_search_fields = new PostSearchFields($request);
        $post_search_fields->excludeFormIds($surveys_with_private_location);
        $query = new self();
        $query->setPaging($request);
        $query->setSearchFields(new PostSearchFields($request));
        return $query;
    }
}
