<?php

namespace Ushahidi\Modules\V5\Actions\Config\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Models\Post\Post;
use Illuminate\Http\Request;

class FindConfigByNameQuery implements Query
{
    private $group_name;



    public function __construct(string $group_name)
    {
        $this->group_name = $group_name;
    }
    public function getGroupName(): string
    {
        return $this->group_name;
    }
}
