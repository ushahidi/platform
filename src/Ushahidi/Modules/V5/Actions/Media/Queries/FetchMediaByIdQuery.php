<?php

namespace Ushahidi\Modules\V5\Actions\Media\Queries;

use App\Bus\Query\Query;

class FetchMediaByIdQuery implements Query
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
