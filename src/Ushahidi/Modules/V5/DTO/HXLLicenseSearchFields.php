<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class HXLLicenseSearchFields
{
    
    private $name;
    private $code;

    public function __construct(Request $request)
    {
        $this->name = $request->query('name');
        $this->code = $request->query('code');
    }

    public function name()
    {
        return $this->name;
    }
    public function code()
    {
        return $this->code;
    }
}
