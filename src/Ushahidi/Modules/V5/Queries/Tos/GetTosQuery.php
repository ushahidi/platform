<?php

namespace Ushahidi\Modules\V5\Queries\Tos;

use App\Bus\Query\Query;
use \Illuminate\Http\Request;
use Ushahidi\Modules\V5\Common\Pageable;
use Ushahidi\Modules\V5\Common\Sortable;
use Ushahidi\Modules\V5\Common\Searchable;

class GetTosQuery implements Query
{
    use Pageable;
    use Sortable;
    use Searchable;

    /**
     * int
     */
    private $id;
    
     /**
     * @var bool
     */
    private $isList = false;

 

    public function __construct(Request $request, int $id = 0)
    {

        $this->id = $id;
        if ($id <= 0) {
            $this->isList = true;
            $this->setPagingParameters($request);
            $this->setSearchParams($request);
            $this->setSortingParameters($request);
        }
    }

    public function getId(): int
    {
        return $this->id;
    }
    
    public function isList(): bool
    {
        return $this->isList;
    }
}
