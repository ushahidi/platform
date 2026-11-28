<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveySearchFields extends SearchFields
{
    /**
     * @var ?string
     */
    protected $query;

    public $showUnknownForm;
    private $role;

    public function __construct(Request $request)
    {
        $this->query = $request->query('q');
        $this->showUnknownForm = $request->query('show_unknown_form', false);
        if (Auth::user()) {
            $this->role = Auth::user()->role;
        } else {
            $this->role = null;
        }
    }

    public function q(): ?string
    {
        return $this->query;
    }

    public function role(): ?string
    {
        return $this->role;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
