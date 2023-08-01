<?php

namespace Ushahidi\Modules\V5\Repository\Apikey;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\Apikey;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\ApikeySearchFields;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\Apikey as ApikeyEntity;

class EloquentApikeyRepository implements ApikeyRepository
{
    private function setSearchCondition(ApikeySearchFields $search_fields, $builder)
    {
        return $builder;
    }
    /**
     * This method will fetch all the Apikey for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param ApikeySearchFields user_search_fields
     * @return Apikey[]
     */
    public function fetch(Paging $paging, ApikeySearchFields $search_fields): LengthAwarePaginator
    {
        return $this->setSearchCondition(
            $search_fields,
            Apikey::take($paging->getLimit())
                ->skip($paging->getSkip())
                ->select(["id","api_key","created","updated"])
                ->orderBy($paging->getOrderBy(), $paging->getOrder())
        )->paginate($paging->getLimit() ? $paging->getLimit() : config('paging.default_laravel_pageing_limit'));
    }

    /**
     * This method will fetch a single Apikey from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Apikey
     * @throws NotFoundException
     */
    public function findById(int $id): Apikey
    {
        $apikey = Apikey::select(["id","api_key","created","updated"])->where("id", "=", $id)->first();
        if (!$apikey instanceof Apikey) {
            throw new NotFoundException('Apikey not found');
        }
        return $apikey;
    }


    /**
     * This method will create a Apikey
     * @param ApikeyEntity $entity
     * @return int
     * @throws \Exception
     */
    public function create(ApikeyEntity $entity): int
    {
        DB::beginTransaction();
        try {
            $apikey = Apikey::create($entity->asArray());
            DB::commit();
            return $apikey->id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will update the Apikey
     * @param int @id
     * @param ApikeyEntity $entity
     * @throws NotFoundException
     */
    public function update(int $id, ApikeyEntity $entity): void
    {
        $apikey = Apikey::find($id);
        if (!$apikey instanceof Apikey) {
            throw new NotFoundException('Apikey not found');
        }

        DB::beginTransaction();
        try {
            Apikey::find($id)->fill($entity->asArray())->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will create a Apikey
     * @param int $id
     * @return int
     * @throws NotFoundException
     */
    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }
}
