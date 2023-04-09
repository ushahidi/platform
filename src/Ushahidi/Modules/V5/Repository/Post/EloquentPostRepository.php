<?php

namespace Ushahidi\Modules\V5\Repository\Post;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\PostSearchFields;

class EloquentPostRepository implements PostRepository
{
    private $queryBuilder;

    public function __construct(Builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }


    private function setSearchCondition(PostSearchFields $search_fields, $query)
    {
        if ($search_fields->q()) {
            if (is_numeric($search_fields->q())) {
                $query->where('id', '=', $search_fields->q());
            } else {
                $query->whereRaw(
                    '(title like ? OR content like ?)',
                    ["%" . $search_fields->q() . "%", "%" . $search_fields->q() . "%"]
                );
            }
        }

        if ($search_fields->postID()) {
            if (is_numeric($search_fields->postID())) {
                $query->where('id', '=', $search_fields->postID());
            }
        }

        if (count($search_fields->status())) {
                $query->whereIn('status', $search_fields->status());
        }

        if ($search_fields->locale()) {
            $query->where('locale', '=', $search_fields->locale());
        }

        if ($search_fields->slug()) {
            $query->where('slug', '=', $search_fields->slug());
        }

        // if ($search_fields->type()) {
        //     $query->where('type', '=', $search_fields->type());
        // }

        // if ($search_fields->tags()) {
        //     $query->where('tag', '=', $search_fields->tags());
        // }
        // if ($search_fields->parent()) {
        //     $query->where('parent_id', '=', $search_fields->parent());
        // }
        return $query;
    }


    public function findById(int $id, array $fields = [], array $with = []): Post
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

    public function paginate(
        Paging $paging,
        PostSearchFields $search_fields,
        array $fields = [],
        array $with = []
    ): LengthAwarePaginator {

        $query = Post::take($paging->getLimit())
            ->skip($paging->getSkip())
            ->orderBy($paging->getOrderBy(), $paging->getOrder());

        if (count($fields)) {
            $query->select($fields);
        }
        if (count($with)) {
            $query->with($with);
        }

        $query = $this->setSearchCondition($search_fields, $query);
        return $query->paginate($paging->getLimit());
    }

    public function delete(int $id): void
    {
        $this->findById($id, ['id'])->delete();
    }
}
