<?php

namespace Ushahidi\Modules\V5\Actions\DataProvider\Queries;

use App\Bus\Query\Query;

class FetchDataProviderByIdQuery implements Query
{
    /**
     * string
     */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
