<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;

class DataProvideSearchFields extends SearchFields
{
    /**
     * @var ?string
     */
    private $type;
    public function __construct(Request $request)
    {
        $this->type = $request->query('type');
    }

    public function type()
    {
        return $this->type;
    }
}
