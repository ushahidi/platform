<?php

namespace Ushahidi\Core\Eloquent\Critieria;

use Illuminate\Support\Arr;
use Ushahidi\Core\Eloquent\Criterion;

class EagerLoad implements Criterion
{
    /**
     * @var array
     */
    protected $relations;

    /**
     * EagerLoad constructor.
     *
     * @param mixed ...$relations
     */
    public function __construct(...$relations)
    {
        $this->relations = Arr::flatten($relations);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|mixed $model
     *
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    public function apply($model)
    {
        return $model->with($this->relations);
    }
}
