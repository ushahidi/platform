<?php

namespace Ushahidi\Modules\V5\Repository\Set;

use SetPostUserNullOnDelete;
use Ushahidi\Modules\V5\Models\SetPost;
use Illuminate\Pagination\LengthAwarePaginator;
use Ushahidi\Modules\V5\DTO\CollectionSearchFields;
use Ushahidi\Core\Entity\Set as CollectionEntity;

interface SetPostRepository
{
    
    /**
     * This method will fetch a single Set from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @param bool $search
     * @return SetPost
     * @throws NotFoundException
     */
    public function findById(int $collection_id, int $post_id): SetPost;

    /**
     * This method will create a Set
     * @param int $collection_id
     * @param int $post_id
     *
     */
   
    public function create(int $collection_id, int $post_id): void;

    /**
     * This method will delete the Set post
     * @param int $collection_id
     * @param int $post_id
     */
    public function delete(int $collection_id, int $post_id): void;
}
