<?php

namespace Ushahidi\Modules\V5\Repository\Post;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\Post\Post;

class EloquentPostRepository implements PostRepository
{
    private $queryBuilder;

    public function __construct(Builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function fetchById(int $id, array $fields = [], array $with = []): Post
    {
        $query = Post::where('id', '=', $id);
        if (count($fields)) {
            $query->select($fields);
        }
        if (count($with)) {
            $query->with($with);
        }
       
        $post = $query->first();


        // ? Post::select($fields)->where('id', '=', $id)->first()
        // : Post::find($id);
        // if(!count($fields)){
        //     $post = $this->queryBuilder->find($id);
        // }else{
        //     $post = $this->queryBuilder->find($id);
        // }

        if (!$post instanceof Post) {
            throw new NotFoundException('Post not found', 404);
        }

        return $post;
    }

    public function paginate(int $limit, array $fields = [], array $with = []): LengthAwarePaginator
    {

        

        $query = Post::take($limit);
        if (count($fields)) {
            $query->select($fields);
        }
        if (count($with)) {
            $query->with($with);
        }
       
        return $query->paginate($limit);

        // if (empty($fields)) {
        //     $fields = ['*'];
        // }

        // return $this->queryBuilder->paginate($limit, $fields);
    }
}
