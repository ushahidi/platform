<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class HXLOrganizationSearchFields
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
