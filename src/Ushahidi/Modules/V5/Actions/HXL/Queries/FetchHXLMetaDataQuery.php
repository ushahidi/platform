<?php

namespace Ushahidi\Modules\V5\Actions\HXL\Queries;

use App\Bus\Query\Query;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\HXLMetadataSearchFields;

class FetchHXLMetaDataQuery implements Query
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
        HXLMetadataSearchFields $search_fields
    ) {
        $this->paging = $paging;
        $this->search_fields = $search_fields;
    }

    public static function fromRequest(Request $request): self
    {
        return new self(Paging::fromRequest($request), new HXLMetadataSearchFields($request));
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
