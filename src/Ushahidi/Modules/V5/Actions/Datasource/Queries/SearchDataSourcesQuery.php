<?php

namespace Ushahidi\Modules\V5\Actions\Datasource\Queries;

use App\Bus\Query\Query;

class SearchDataSourcesQuery implements Query
{
    private $type;

    public function __construct(?string $type)
    {
        $this->type = $type;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
}
