<?php

namespace Ushahidi\Modules\V5\Repository\Post;

use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\PostLock;
use Ushahidi\Core\Exception\NotFoundException;

interface PostLockRepository
{
    public function findById(int $id): PostLock;
    public function findByPostId(int $post_id): PostLock;
    public function findByUserId(int $user_id): PostLock;

    public function deleteById(int $id): void;
    public function deleteByPostId(int $post_id): void;
    public function deleteByUserId(int $user_id): void;

    public function create(array $input): int;
}
