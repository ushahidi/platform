<?php

namespace Ushahidi\Modules\V5\Repository\Tos;

use Ushahidi\Modules\V5\Models\Tos;
use Illuminate\Pagination\LengthAwarePaginator;

interface TosRepository
{
 /**
     * This method will fetch all the Tos for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @return Tos[]
     */
    public function fetch(int $limit, int $skip, string $sortBy, string $order): LengthAwarePaginator;

    /**
     * This method will fetch a single Tos from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Tos
     * @throws NotFoundException
     */
    public function findById(int $id): Tos;

    /**
     * This method will create a Tos
     * @param array $data
     * @return int
     */
    public function Create(array $data): int;
}
