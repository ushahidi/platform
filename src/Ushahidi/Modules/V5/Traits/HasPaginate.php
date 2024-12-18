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
    private $default_limit = 20;
    private $default_page = 1;

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
            $default_sort_by ?? $this->default_sort_by,
            $default_order ?? $this->default_order,
            $default_limit ?? $this->default_limit,
            $default_page ?? $this->default_page
        );
    }

    public function setDefaultOrder($order): void
    {
        $this->default_order = $order;
    }
    public function setDefaultSort($sort): void
    {
        $this->default_sort_by = $sort;
    }
    public function setDefaultLimit($limit): void
    {
        $this->default_limit = $limit;
    }

    public function setDefaultPage($page): void
    {
        $this->page = $page;
    }
}
