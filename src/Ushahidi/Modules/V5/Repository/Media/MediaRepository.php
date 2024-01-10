<?php

namespace Ushahidi\Modules\V5\Repository\Media;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Media;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\MediaSearchFields;
use Ushahidi\Core\Entity\Media as MediaEntity;

interface MediaRepository
{

    /**
     * This method will fetch all the Media for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param MediaSearchFields user_search_fields
     * @return LengthAwarePaginator
     */
    public function fetch(Paging $paging, MediaSearchFields $search_fields): LengthAwarePaginator;

    /**
     * This method will fetch a single Media from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Media
     * @throws NotFoundException
     */
    public function findById(int $id): Media;

    /**
     * This method will create a Media
     * @param MediaEntity $entity
     * @return int
     */
    public function create(MediaEntity $entity): int;

    /**
     * This method will update the Media
     * @param int $id
     * @param MediaEntity $entity
     */
    public function update(int $id, MediaEntity $entity): void;

    /**
     * This method will delete the Media
     * @param int $id
     */
    public function delete(int $id): void;
}
