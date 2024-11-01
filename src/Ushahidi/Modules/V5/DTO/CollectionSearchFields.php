<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionSearchFields extends SearchFields
{
    /**
     * @var ?string
     */
    protected $query;
    protected $is_saved_search;
    protected $role;
    private $name;
    private $keyword;
    private $user_id;

    public function __construct(Request $request)
    {
        $this->query = $request->query('q');
        $this->is_saved_search = 0;

        if (Auth::user()) {
            $this->role = Auth::user()->role;
            $this->user_id = Auth::user()->id;
        } else {
            $this->role = null;
            $this->user_id = null;
        }

        $this->name = $request->query('name');
        $this->keyword = $request->query('keyword');
    }

    public function q(): ?string
    {
        return $this->query;
    }

    public function isSavedSearch()
    {
        return $this->is_saved_search;
    }

    public function role()
    {
        return $this->role;
    }

    public function name()
    {
        return $this->name;
    }

    public function keyword()
    {
        return $this->keyword;
    }
    public function userId()
    {
        return $this->user_id;
    }
    public function isAdmin()
    {
        return ($this->role === "admin");
    }
}
