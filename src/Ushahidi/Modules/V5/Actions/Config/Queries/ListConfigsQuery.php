<?php

namespace Ushahidi\Modules\V5\Actions\Config\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Models\Post\Post;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\ConfigSearchFields;

class ListConfigsQuery implements Query
{
    private $search_fields;
    public function __construct(
        ConfigSearchFields $search_fields
    ) {
        $this->search_fields = $search_fields;
    }

    public function getSearchFields()
    {
        return $this->search_fields;
    }
}
