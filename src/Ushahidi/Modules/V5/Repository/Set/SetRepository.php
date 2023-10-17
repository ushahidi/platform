<?php

namespace Ushahidi\Modules\V5\Repository\Set;

use Ushahidi\Modules\V5\Models\Set;
use Ushahidi\Core\Entity\Set as CollectionEntity;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Core\Tool\SearchData;
use Ushahidi\Modules\V5\DTO\CollectionSearchFields;

interface SetRepository
{
    /**
     * This method will fetch all the Set for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param bool $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<Set>
     */
    public function fetch();

    /**
     * This method will fetch a single Set from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @param bool $search
     * @return Set
     * @throws NotFoundException
     */
    public function findById(int $id, bool $search = false): Set;

    /**
     * This method will create a Set
     * @param CollectionEntity $data
     * @return int
     */
    public function create(CollectionEntity $data): int;

    /**
     * This method will update the Set
     * @param int $id
     * @param CollectionEntity $set_entity
     */
    public function update(int $id, CollectionEntity $set_entity, bool $search = false): void;

    /**
     * This method will delete the Set
     * @param int $id
     */
    public function delete(int $id, bool $search = false): void;

    public function setSearchParams(SearchData $searchData);
}
