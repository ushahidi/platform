<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class ConfigSearchFields extends SearchFields
{
    /**
     * @var array
     */
    private $groups;
    public function __construct(Request $request)
    {
        $this->groups = ParameterUtilities::getParameterAsArray($request->get('groups'));
    }

    public function groups()
    {
        return $this->groups;
    }
}
