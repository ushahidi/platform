<?php

namespace Ushahidi\App\Bus\Query\Example;

use Ushahidi\App\Bus\Query\Query;

class ExampleQuery implements Query
{
    private $index;

    public function __construct(int $index)
    {
        $this->index = $index;
    }

    public function getIndex(): int
    {
        return $this->index;
    }
}
