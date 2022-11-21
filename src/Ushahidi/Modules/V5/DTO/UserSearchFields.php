<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;

class UserSearchFields
{
    /**
     * @var ?string
     */
    private $query;

    /**
     * @var ?array
     */
    private $role;

    public function __construct(Request $request)
    {
        $this->query = $request->query('q');
        $this->role =
            $request->query('role')
            ? explode(',', $request->query('role'))
            : null;
    }

    public function q(): ?string
    {
        return $this->query;
    }

    public function role(): ?array
    {
        return $this->role;
    }

    public function roleAsMysqlInCondition(): ?string
    {
        $role_in_condition = "(";
        foreach ($this->role as $key => $item) {
            $role_in_condition .= $key==0 ? $item."," : $item;
        }
        return $role_in_condition.= ")";
    }
}
