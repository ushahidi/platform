<?php

namespace Ushahidi\Modules\V5\Actions\Post\Queries;

use App\Bus\Query\Query;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\DTO\PostStatsSearchFields;

class PostsStatsQuery implements Query
{
    private $search_fields;
      
    private function __construct(
        PostStatsSearchFields $search_fields
    ) {
        $this->search_fields = $search_fields;
    }

    public function getSearchFields()
    {
        return $this->search_fields;
    }

    public static function fromRequest(Request $request, array $surveys_with_private_location = []): self
    {
        $post_search_fields = new PostStatsSearchFields($request);
        $post_search_fields->excludeFormIds($surveys_with_private_location);
        return new self(new PostStatsSearchFields($request));
    }
}
