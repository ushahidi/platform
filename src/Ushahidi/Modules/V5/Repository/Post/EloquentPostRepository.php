<?php

namespace Ushahidi\Modules\V5\Repository\Post;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Http\Resources\PostCollection;
use Ushahidi\Modules\V5\Models\Post\Post;

class EloquentPostRepository implements PostRepository
{
    private $queryBuilder;

    public function __construct(Builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function fetchById(int $id): Post
    {
        $post = $this->queryBuilder->find($id);

        if (!$post instanceof Post) {
            throw new NotFoundException('Post not found', 404);
        }

        return $post;
    }

    public function paginate(int $limit, array $fields): LengthAwarePaginator
    {
        if (empty($fields)) {
            $fields = ['*'];
        }

        return $this->queryBuilder->paginate($limit, $fields);
    }
}
