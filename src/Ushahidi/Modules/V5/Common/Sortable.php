<?php


namespace Ushahidi\Modules\V5\Common;

use \Illuminate\Http\Request;

trait Sortable
{

    /**
     * @var string
     */
    private $order;

    /**
     * @var string
     */
    private $order_by;

    private function setSortingParameters(Request $request):void
    {
         $this->order =
            $this->hasCustomOrder($request)?$request->get("order"):config('paging.default_order');
        $this->order_by =
            $this->hasCustomOrderBy($request)?$request->get("order_by"):config('paging.default_order_by');
    }
    public function order(): string
    {
        return $this->order;
    }

    public function orderBy(): string
    {
        return $this->order_by;
    }

    private function hasCustomOrder(Request $request):bool
    {
        // #TODO check order one of value is one of columns
        return ($request->has("order") );
    }

    private function hasCustomOrderBy(Request $request):bool
    {
        return ($request->has("order_by") && in_array(strtolower($request->get("order_by")), ["asc","desc"]));
    }
}
