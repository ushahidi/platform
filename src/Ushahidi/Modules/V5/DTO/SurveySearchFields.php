<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;

class SurveySearchFields
{
    /**
     * @var ?string
     */
    private $query;

    public function __construct(Request $request)
    {
        $this->query = $request->query('q');
    }

    public function q(): ?string
    {
        return $this->query;
    }
}
