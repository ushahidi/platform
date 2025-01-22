<?php

namespace Ushahidi\Modules\V5\Actions\Datasource\Queries;

use App\Bus\Query\Query;

class FetchDataSourceQuery implements Query
{
    private $source;

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
