<?php

namespace Ushahidi\Modules\V5\Actions\Apikey\Queries;

use App\Bus\Query\Query;

class FetchApikeyByIdQuery implements Query
{


    /**
     * int
     */
    private $id;

    public function __construct(int $id = 0)
    {

        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
