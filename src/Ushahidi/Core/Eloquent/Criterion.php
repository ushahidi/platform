<?php

namespace Ushahidi\Core\Eloquent;

interface Criterion
{
    /**
     * @param mixed $model
     *
     * @return mixed
     */
    public function apply($model);
}
