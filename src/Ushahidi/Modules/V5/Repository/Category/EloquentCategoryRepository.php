<?php

namespace Ushahidi\Modules\V5\Repository\Category;

use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Category;

class EloquentCategoryRepository implements CategoryRepository
{
    public function fetchByIdOrFail(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function fetchAll(): Collection
    {
        return Category::all();
    }
}
