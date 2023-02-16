<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;

class CollectionSearchFields
{
    /**
     * @var ?string
     */
    protected $query;

    protected $search;

    public function __construct(Request $request)
    {
        $this->query = $request->query('q');
        $this->search = false;
    }

    public function q(): ?string
    {
        return $this->query;
    }

    public function search(): bool
    {
        return $this->search;
    }
}
