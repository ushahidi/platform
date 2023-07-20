<?php

namespace Ushahidi\Modules\V5\Repository\CSV;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\CSV;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\CSVSearchFields;
use Ushahidi\Core\Entity\CSV as CSVEntity;

interface CSVRepository
{

    /**
     * This method will fetch all the CSV for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param CSVSearchFields user_search_fields
     * @return LengthAwarePaginator
     */
    public function fetch(Paging $paging, CSVSearchFields $search_fields): LengthAwarePaginator;

    /**
     * This method will fetch a single CSV from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return CSV
     * @throws NotFoundException
     */
    public function findById(int $id): CSV;

    /**
     * This method will create a CSV
     * @param CSVEntity $entity
     * @return int
     */
    public function create(CSVEntity $entity): int;

    /**
     * This method will update the CSV
     * @param int $id
     * @param CSVEntity $entity
     */
    public function update(int $id, CSVEntity $entity): void;

       /**
     * This method will delete the CSv
     * @param int $id
     */
    public function delete(int $id): void;
}
