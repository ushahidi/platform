<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategorySearchFields extends SearchFields
{
    /**
     * @var ?string
     */
    private $query;
    private $tag;
    private $type;
    private $level;
    private $parent_id;
    private $role;
    private $user_id;


    public function __construct(Request $request)
    {
        $this->query = $request->query('q');
        $this->tag = $request->query('tag');
        $this->type = $request->query('type');
        $this->level = $request->query('level');
        $this->parent_id = $request->query('parent_id');
        if (Auth::user()) {
            $this->role = Auth::user()->role;
            $this->user_id = Auth::user()->id;
        } else {
            $this->role = null;
            $this->user_id = null;
        }
    }

    public function q(): ?string
    {
        return $this->query;
    }

    public function tag()
    {
        return $this->tag;
    }

    public function type()
    {
        return $this->type;
    }

    public function level()
    {
        return $this->level;
    }

    public function parentId()
    {
        return $this->parent_id;
    }

    public function role(): ?string
    {
        return $this->role;
    }

    public function isAdmin()
    {
        return ($this->role === "admin");
    }
    public function userId()
    {
        return $this->user_id;
    }
    public function isParent()
    {
        return ($this->level === "parent");
    }
}
