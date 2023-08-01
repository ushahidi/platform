<?php

namespace Ushahidi\Modules\V5\Repository\Apikey;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Apikey;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\ApikeySearchFields;
use Ushahidi\Core\Entity\ApiKey as ApikeyEntity;

interface ApikeyRepository
{

    /**
     * This method will fetch all the Apikey for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param ApikeySearchFields user_search_fields
     * @return LengthAwarePaginator
     */
    public function fetch(Paging $paging, ApikeySearchFields $search_fields): LengthAwarePaginator;

    /**
     * This method will fetch a single Apikey from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Apikey
     * @throws NotFoundException
     */
    public function findById(int $id): Apikey;

    /**
     * This method will create a Apikey
     * @param ApikeyEntity $entity
     * @return int
     */
    public function create(ApikeyEntity $entity): int;

    /**
     * This method will update the Apikey
     * @param int $id
     * @param ApikeyEntity $entity
     */
    public function update(int $id, ApikeyEntity $entity): void;

       /**
     * This method will delete the Apikey
     * @param int $id
     */
    public function delete(int $id): void;
}
