<?php


namespace Ushahidi\Modules\V5\Common;

use \Illuminate\Http\Request;

trait Pageable
{

    /**
     * @var int
     */
    private $per_page;

    private function setPagingParameters(Request $request):void
    {
         $this->per_page =
         $this->hasCustomPerPage($request)?(int)$request->get("per_page"):config('paging.items_per_page');
    }
    public function perPage(): int
    {
        return $this->per_page;
    }

    private function hasCustomPerPage(Request $request):bool
    {
        return ($request->has("per_page") && is_numeric($request->get("per_page")));
    }
}
