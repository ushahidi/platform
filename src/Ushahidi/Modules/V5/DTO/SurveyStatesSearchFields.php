<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;

class SurveyStatesSearchFields
{

    private $created_after;
    private $created_before;

    public function __construct(Request $request)
    {
        $this->created_after = $request->query('created_after');
        $this->created_before = $request->query('created_before');
    }

    public function createdAfter()
    {
        return $this->created_after;
    }
    public function createdBefore()
    {
        return $this->created_before;
    }
}
