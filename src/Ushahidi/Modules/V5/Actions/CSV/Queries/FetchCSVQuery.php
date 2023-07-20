<?php

namespace Ushahidi\Modules\V5\Actions\CSV\Queries;

use App\Bus\Query\Query;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\CSVSearchFields;

class FetchCSVQuery implements Query
{
    const DEFAULT_LIMIT = 0;
    const DEFAULT_ORDER = "DESC";
    const DEFAULT_SORT_BY = "featured";

    /**
     * @var Paging
     */
    private $paging;
    private $search_fields;

    private function __construct(
        Paging $paging,
        CSVSearchFields $search_fields
    ) {
        $this->paging = $paging;
        $this->search_fields = $search_fields;
    }

    public static function fromRequest(Request $request): self
    {
        return new self(Paging::fromRequest($request), new CSVSearchFields($request));
    }

    public function getPaging(): Paging
    {
        return $this->paging;
    }

    public function getSearchFields()
    {
        return $this->search_fields;
    }
}
