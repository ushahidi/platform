<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;

class SavedSearchSearchFields extends CollectionSearchFields
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->is_saved_search = 1;
    }
}
