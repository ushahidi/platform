<?php

namespace Ushahidi\Core\Eloquent\Critieria;

use Ushahidi\Core\Eloquent\Criterion;

class Order implements Criterion
{
    /**
     * @var string
     */
    protected $column;
    /**
     * @var string
     */
    protected $sortBy;

    /**
     * Order constructor.
     *
     * @param string $column
     * @param string $sortBy
     */
    public function __construct(string $column, string $sortBy)
    {
        $this->column = $column;
        $this->sortBy = $sortBy;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|mixed $model
     *
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    public function apply($model)
    {
        return $model->orderBy($this->column, $this->sortBy);
    }
}
