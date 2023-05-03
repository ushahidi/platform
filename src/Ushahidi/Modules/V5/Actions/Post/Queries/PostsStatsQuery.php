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

    public static function fromRequest(Request $request): self
    {

        return new self(new PostStatsSearchFields($request));
    }
}
