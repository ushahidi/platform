<?php

namespace Ushahidi\Modules\V5\Actions\Role\Queries;

use App\Bus\Query\Query;

class FetchRoleByIdQuery implements Query
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
