<?php

namespace Ushahidi\Modules\V5\Actions\Contact\Queries;

use App\Bus\Query\Query;

class FetchContactByIdQuery implements Query
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
