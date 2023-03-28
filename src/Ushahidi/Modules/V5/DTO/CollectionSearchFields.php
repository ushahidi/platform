<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionSearchFields
{
    /**
     * @var ?string
     */
    protected $query;

    protected $search;

    protected $role;

    public function __construct(Request $request)
    {
        $this->query = $request->query('q');
        $this->search = false;
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

    public function search(): bool
    {
        return $this->search;
    }

    public function role(): ?string
    {
        return $this->role;
    }
}
