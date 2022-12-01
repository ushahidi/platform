<?php

namespace Ushahidi\Modules\V5\Repository\Category;

use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Category;

interface CategoryRepository
{
    /**
     * @throws \Exception if category is not found
     */
    public function fetchByIdOrFail(int $id): Category;

    public function fetchAll(): Collection;
}
