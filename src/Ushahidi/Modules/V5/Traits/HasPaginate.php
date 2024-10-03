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
    private $default_order;
    private $default_sort_by = 'id';
    private $default_limit = '20';

    public function getPaging(): Paging
    {
        return $this->paging;
    }
    
    public function setPaging(
        Request $request,
        $default_sort_by = null,
        $default_order = null,
        $default_limit = null,
        $default_page = 1
    ) {
        $this->paging =  Paging::fromRequest(
            $request,
            $default_sort_by,
            $default_order,
            $default_limit,
            $default_page
        );
    }

    public function setDefaultOrder(): void
    {
         $this->paging;
    }
}
