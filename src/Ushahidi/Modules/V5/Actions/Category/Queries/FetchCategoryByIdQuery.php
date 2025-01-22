<?php

namespace Ushahidi\Modules\V5\Actions\Category\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Models\Category;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Traits\OnlyParameter\QueryWithOnlyParameter;

class FetchCategoryByIdQuery implements Query
{
    use QueryWithOnlyParameter;

    private $id;

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
        $query->addOnlyParameteresFromRequest(
            $request,
            Category::ALLOWED_FIELDS,
            Category::ALLOWED_RELATIONSHIPS,
            Category::REQUIRED_FIELDS
        );
        return $query;
    }
    public function getId(): int
    {
        return $this->id;
    }
}
