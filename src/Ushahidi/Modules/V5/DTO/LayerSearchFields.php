<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class LayerSearchFields extends SearchFields
{
    
    private $type;
    private $active;
    private $has_active_filter;

    public function __construct(Request $request)
    {
        $this->type = $request->query('type');

        if ($request->query('active')) {
            $this->has_active_filter = true;
            $this->active = 1;
        } elseif (!ParameterUtilities::checkIfNullOrEmpty($request->query('active'))) {
            $this->has_active_filter = true;
            $this->active = 0;
        } else {
            $this->has_active_filter = false;
        }
    }
    
    public function type()
    {
        return $this->type;
    }

    public function active()
    {
        return $this->active;
    }


    public function hasActiveFilter()
    {
        return $this->has_active_filter;
    }
}
