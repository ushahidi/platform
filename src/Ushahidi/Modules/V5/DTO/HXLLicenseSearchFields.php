<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;

class HXLLicenseSearchFields extends SearchFields
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
