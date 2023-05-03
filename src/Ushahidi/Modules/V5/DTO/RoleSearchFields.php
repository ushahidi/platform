<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;

class RoleSearchFields
{
    /**
     * @var ?string
     */
    private $q;

    /**
     * @var ?string
     */
    private $name;

    public function __construct(Request $request)
    {
        $this->q = $request->query('q');
        $this->name = $request->query('name');
    }

    public function q(): ?string
    {
        return $this->q;
    }

    public function name(): ?string
    {
        return $this->name;
    }
}
