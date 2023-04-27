<?php

namespace Ushahidi\Modules\V5\Repository\Post;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\PostSearchFields;

interface PostRepository
{
    public function findById(int $id, array $fields = [], array $with = []): Post;

    public function paginate(
        Paging $paging,
        PostSearchFields $search_fields,
        array $fields = [],
        array $with = []
    ): LengthAwarePaginator;

    public function delete(int $id): void;

    public function getCountOfPosts(PostSearchFields $search_fields):int;

    public function getPostsGeoJson(Paging $paging, PostSearchFields $search_fields);
    public function getPostGeoJson(int $post_id);

    public function getPostsStats(PostSearchFields $search_fields);
}
