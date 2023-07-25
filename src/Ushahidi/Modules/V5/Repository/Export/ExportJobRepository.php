<?php

namespace Ushahidi\Modules\V5\Repository\Export;

use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\Models\ExportJob;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\DTO\ExportJobSearchFields;
use Ushahidi\Core\Entity\ExportJob as ExportJobEntity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ExportJobRepository
{

    /**
     * This method will fetch all the ExportJob for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param ExportJobSearchFields user_search_fields
     * @return LengthAwarePaginator
     */
    public function fetch(Paging $paging, ExportJobSearchFields $search_fields): LengthAwarePaginator;

    /**
     * This method will fetch a single ExportJob from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return ExportJob
     * @throws NotFoundException
     */
    public function findById(int $id): ExportJob;

    /**
     * This method will create a ExportJob
     * @param ExportJobEntity $entity
     * @return int
     */
    public function create(ExportJobEntity $entity): int;

    /**
     * This method will update the ExportJob
     * @param int $id
     * @param ExportJobEntity $entity
     */
    public function update(int $id, ExportJobEntity $entity): void;

       /**
     * This method will delete the ExportJob
     * @param int $id
     */
    public function delete(int $id): void;
}
