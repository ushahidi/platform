<?php

namespace Ushahidi\Modules\V5\Actions\SavedSearch\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Traits\OnlyParameter\QueryWithOnlyParameter;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Models\Set as SavedSearch;

class FetchSavedSearchByIdQuery implements Query
{
    use QueryWithOnlyParameter;

    /**
     * int
     */
    private $id;

    public function __construct(int $id = 0)
    {
        $this->id = $id;
    }

    public static function fromRequest(int $id, Request $request): self
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Id must be a positive number');
        }
        $query =  new self($id);
        $excluded_relations = ['posts'];
        $query->addOnlyParameteresFromRequest($request, SavedSearch::ALLOWED_FIELDS, SavedSearch::ALLOWED_RELATIONSHIPS, SavedSearch::REQUIRED_FIELDS, $excluded_relations);
        return $query;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
