<?php

namespace Ushahidi\Modules\V5\Repository\Permissions;

use Ushahidi\Modules\V5\Models\Permissions;
use Illuminate\Pagination\LengthAwarePaginator;

interface PermissionsRepository
{
 /**
     * This method will fetch all the Permissions for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @return Permissions[]
     */
    public function fetch(
        int $limit,
        int $skip,
        string $sortBy,
        string $order,
        array $search_data
    ): LengthAwarePaginator;

    /**
     * This method will fetch a single Permissions from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Permissions
     * @throws NotFoundException
     */
    public function findById(int $id): Permissions;
}
