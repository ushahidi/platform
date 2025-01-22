<?php

namespace Ushahidi\Modules\V5\Actions\Post\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Models\Post\Post;
use Illuminate\Http\Request;

class FindPostGeometryByIdQuery implements Query
{
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
        return new self($id);
    }

    public function getId(): int
    {
        return $this->id;
    }
}
