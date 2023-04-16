<?php

namespace Ushahidi\Modules\V5\Repository\Set;

use Ushahidi\Modules\V5\Models\SetPost;
use Ushahidi\Modules\V5\Repository\Set\SetPostRepository;
use Ushahidi\Core\Exception\NotFoundException;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\Set as CollectionEntity;

class EloquentSetPostRepository implements SetPostRepository
{
   
      /**
     * This method will fetch a single Set from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $collection_id
     * @param int $post_id
     * @return SetPost
     * @throws NotFoundException
     */
    public function findById(int $collection_id, int $post_id): SetPost
    {
        $set_post = SetPost::where('post_id', '=', $post_id)->where('set_id', '=', $collection_id)->first();
        if (!$set_post instanceof SetPost) {
            throw new NotFoundException('post '.$post_id.' not found in set '.$collection_id);
        }
        return $set_post;
    }

    /**
     * This method will create a Set
     * @param int $collection_id
     * @param int $post_id
     *
     */
   
    public function create(int $collection_id, int $post_id): void
    {
        $setPost = setPost::create(['set_id'=>$collection_id,'post_id'=>$post_id]);
    }

    /**
     * This method will delete the Set post
     * @param int $collection_id
     * @param int $post_id
     */
    public function delete(int $collection_id, int $post_id): void
    {
        $this->findById($collection_id, $post_id)->delete();
    }
}
