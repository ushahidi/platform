<?php

namespace Ushahidi\Modules\V5\Repository\User;

use Ushahidi\Modules\V5\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepository
{
 /**
     * This method will fetch all the User for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @return User[]
     */
    public function fetch(
        int $limit,
        int $skip,
        string $sortBy,
        string $order,
        array $search_data
    ): LengthAwarePaginator;

    /**
     * This method will fetch a single User from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return User
     * @throws NotFoundException
     */
    public function findById(int $id): User;

    /**
     * This method will create a User
     * @param array $data
     * @return int
     */
    public function create(array $data): int;

    /**
     * This method will update the User
     * @param int $id
     * @param array $data
     */
    public function update(int $id, array $data): void;

       /**
     * This method will delete the User
     * @param int $id
     */
    public function delete(int $id): void;
}
