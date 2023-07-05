<?php

namespace Ushahidi\Modules\V5\Repository\Layer;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Layer;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\LayerSearchFields;
use Ushahidi\Core\Ohanzee\Entities\Layer as LayerEntity;

interface LayerRepository
{

    /**
     * This method will fetch all the Layer for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param LayerSearchFields user_search_fields
     * @return LengthAwarePaginator
     */
    public function fetch(Paging $paging, LayerSearchFields $search_fields): LengthAwarePaginator;

    /**
     * This method will fetch a single Layer from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Layer
     * @throws NotFoundException
     */
    public function findById(int $id): Layer;

    /**
     * This method will create a Layer
     * @param LayerEntity $entity
     * @return int
     */
    public function create(LayerEntity $entity): int;

    /**
     * This method will update the Layer
     * @param int $id
     * @param LayerEntity $entity
     */
    public function update(int $id, LayerEntity $entity): void;

       /**
     * This method will delete the Layer
     * @param int $id
     */
    public function delete(int $id): void;
}
