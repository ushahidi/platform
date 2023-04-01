<?php

namespace Ushahidi\Modules\V5\Actions\Post\Queries;

use App\Bus\Query\Query;

class FindPostByIdQuery implements Query
{
    private $id;

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function of(int $id): self
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
