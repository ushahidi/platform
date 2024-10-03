<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;

class HXLTagSearchFields extends SearchFields
{
    private $q;
    public function __construct(Request $request)
    {
        $this->q = $request->query('q');
    }

    public function q()
    {
        return $this->q;
    }
}
