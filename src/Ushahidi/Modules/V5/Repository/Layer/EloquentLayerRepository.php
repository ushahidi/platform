<?php

namespace Ushahidi\Modules\V5\Repository\Layer;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\Layer;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\LayerSearchFields;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\Layer as LayerEntity;

class EloquentLayerRepository implements LayerRepository
{
    private function setSearchCondition(LayerSearchFields $search_fields, $builder)
    {
        if ($search_fields->type()) {
            $builder->where('layers.type', '=', $search_fields->type());
        }

        if ($search_fields->hasActiveFilter()) {
            $builder->where('layers.active', '=', $search_fields->active());
        }
        return $builder;
    }
    /**
     * This method will fetch all the Layer for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param LayerSearchFields user_search_fields
     * @return Layer[]
     */
    public function fetch(Paging $paging, LayerSearchFields $search_fields): LengthAwarePaginator
    {
        return $this->setSearchCondition(
            $search_fields,
            Layer::take($paging->getLimit())
                ->skip($paging->getSkip())
                ->orderBy($paging->getOrderBy(), $paging->getOrder())
        )->paginate($paging->getLimit() ? $paging->getLimit() : config('paging.default_laravel_pageing_limit'));
    }

    /**
     * This method will fetch a single Layer from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Layer
     * @throws NotFoundException
     */
    public function findById(int $id): Layer
    {
        $layer = Layer::find($id);
        if (!$layer instanceof Layer) {
            throw new NotFoundException('Layer not found');
        }
        return $layer;
    }


    /**
     * This method will create a Layer
     * @param LayerEntity $entity
     * @return int
     * @throws \Exception
     */
    public function create(LayerEntity $entity): int
    {
        DB::beginTransaction();
        try {
            $layer = Layer::create($entity->asArray());
            DB::commit();
            return $layer->id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will update the Layer
     * @param int @id
     * @param LayerEntity $entity
     * @throws NotFoundException
     */
    public function update(int $id, LayerEntity $entity): void
    {
        $layer = Layer::find($id);
        if (!$layer instanceof Layer) {
            throw new NotFoundException('Layer not found');
        }

        DB::beginTransaction();
        try {
            Layer::find($id)->fill($entity->asArray())->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will create a Layer
     * @param int $id
     * @return int
     * @throws NotFoundException
     */
    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }
}
