<?php

namespace Ushahidi\Modules\V5\Traits;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\DTO\Paging;

trait HasPaginate
{
    /**
     * @var Paging
     */
    private $paging;

    public function getPaging(): Paging
    {
        return $this->paging;
    }
    
    public function setPaging(Request $request)
    {
        $this->paging =  Paging::fromRequest($request);
    }
}
