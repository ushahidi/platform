<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;

class SurveySearchFields extends SearchFields
{
    /**
     * @var ?string
     */
    protected $query;

    public $showUnknownForm;

    public function __construct(Request $request)
    {
        $this->query = $request->query('q');
        $this->showUnknownForm = $request->query('show_unknown_form', false);
    }

    public function q(): ?string
    {
        return $this->query;
    }
}
