<?php

namespace Ushahidi\Modules\V5\Actions\Post\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Models\Post\Post;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Traits\OnlyParameter\QueryWithOnlyParameter;

class FindPostByIdQuery implements Query
{
    private $id;
    use QueryWithOnlyParameter;
    
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromRequest(int $id, Request $request): self
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Id must be a positive number');
        }
        $query =  new self($id);
        $query->addOnlyParameteresFromRequest($request, Post::ALLOWED_FIELDS, Post::ALLOWED_RELATIONSHIPS, Post::REQUIRED_FIELDS);
        return $query;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
