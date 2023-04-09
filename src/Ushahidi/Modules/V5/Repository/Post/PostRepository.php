<?php

namespace Ushahidi\Modules\V5\Repository\Post;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\PostSearchFields;

interface PostRepository
{
    public function fetchById(int $id, array $fields = [], array $with = []): Post;

    public function paginate(Paging $paging, PostSearchFields $search_fields, array $fields = [], array $with = []): LengthAwarePaginator;
}
