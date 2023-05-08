<?php

namespace Ushahidi\Core\Eloquent\Critieria;

class Latest extends Order
{
    /**
     * Latest constructor.
     *
     * @param string $column
     */
    public function __construct(string $column = 'created_at', string $sortBy = 'desc')
    {
        $this->column = $column;
        $this->sortBy = $sortBy;
    }
}
