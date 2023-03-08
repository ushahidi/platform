<?php

namespace Ushahidi\Modules\V5\Repository\Post;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Http\Resources\PostCollection;
use Ushahidi\Modules\V5\Models\Post\Post;

interface PostRepository
{
    public function fetchById(int $id): Post;

    public function paginate(int $limit, array $fields): LengthAwarePaginator;
}
