<?php

namespace Ushahidi\Modules\V5\Repository\Post;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\PostLock;

class EloquentPostLockRepository implements PostLockRepository
{
    public function findById(int $id): PostLock
    {
        $post_lock = PostLock::find($id);
        if (!$post_lock instanceof PostLock) {
            throw new NotFoundException('Post Lock not found');
        }
        return $post_lock;
    }
    public function findByPostId(int $post_id): PostLock
    {
        $post_lock = PostLock::where('post_id', '=', $post_id)->first();
        if (!$post_lock instanceof PostLock) {
            throw new NotFoundException('Post Lock not found');
        }
        return $post_lock;
    }
    public function findByUserId(int $user_id): PostLock
    {
        $post_locks = PostLock::where('user_id', '=', $user_id)->get();
       // do we need to chek the count ?!
        return $post_locks;
    }

    public function deleteById(int $id): void
    {
        $this->findById($id)->delete();
    }
    public function deleteByPostId(int $post_id): void
    {
        $this->findByPostId($post_id)->delete();
    }
    public function deleteByUserId(int $user_id): void
    {
        $post_locks = $this->findByUserId($user_id);
        foreach ($post_locks as $post_lock) {
            $post_lock->delete();
        }
    }

    public function create(array $input):int
    {
        $post_lock = PostLock::create($input);
        return $post_lock->id;
    }
}
