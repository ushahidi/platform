<?php


namespace Ushahidi\Modules\V5\Common;

use \Illuminate\Http\Request;

trait Searchable
{

    /**
     * @var array
     */
    private $search_params;

    private function setSearchParams(Request $request):void
    {
        // To Do check search parameters
        $this->search_params = [];
    }

    public function searchParams(): array
    {
        return $this->search_params;
    }
}
