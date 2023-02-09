<?php

namespace App\Bus\Query\Example;

use App\Bus\Query\Query;

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
