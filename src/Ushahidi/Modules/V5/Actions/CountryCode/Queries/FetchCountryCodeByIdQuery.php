<?php

namespace Ushahidi\Modules\V5\Actions\CountryCode\Queries;

use App\Bus\Query\Query;

class FetchCountryCodeByIdQuery implements Query
{
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
