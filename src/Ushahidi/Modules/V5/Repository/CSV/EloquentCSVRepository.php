<?php

namespace Ushahidi\Modules\V5\Repository\CSV;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\CSV;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\CSVSearchFields;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\CSV as CSVEntity;

class EloquentCSVRepository implements CSVRepository
{
    private function setSearchCondition(CSVSearchFields $search_fields, $builder)
    {
        // if (count($search_fields->columns())) {
        //     $builder->whereIn('csv.columns', $search_fields->columns());
        // }

        if ($search_fields->mapsTo()) {
            $builder->where('csv.maps_to', 'like', "%" .$search_fields->mapsTo(). "%");
        }

        if ($search_fields->fixed()) {
            $builder->where('csv.fixed', 'like', "%" .$search_fields->fixed(). "%");
        }

        if ($search_fields->fileName()) {
            $builder->where('csv.filename', '=', $search_fields->fileName());
        }

        return $builder;
    }
    /**
     * This method will fetch all the CSV for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param CSVSearchFields user_search_fields
     * @return CSV[]
     */
    public function fetch(Paging $paging, CSVSearchFields $search_fields): LengthAwarePaginator
    {
        return $this->setSearchCondition(
            $search_fields,
            CSV::take($paging->getLimit())
                ->skip($paging->getSkip())
                ->orderBy($paging->getOrderBy(), $paging->getOrder())
        )->paginate($paging->getLimit() ? $paging->getLimit() : config('paging.default_laravel_pageing_limit'));
    }

    /**
     * This method will fetch a single CSV from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return CSV
     * @throws NotFoundException
     */
    public function findById(int $id): CSV
    {
        $csv = CSV::find($id);
        if (!$csv instanceof CSV) {
            throw new NotFoundException('CSV not found');
        }
        return $csv;
    }


    /**
     * This method will create a CSV
     * @param CSVEntity $entity
     * @return int
     * @throws \Exception
     */
    public function create(CSVEntity $entity): int
    {
        DB::beginTransaction();
        try {
            $csv = CSV::create($entity->asArray());
            DB::commit();
            return $csv->id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will update the CSV
     * @param int @id
     * @param CSVEntity $entity
     * @throws NotFoundException
     */
    public function update(int $id, CSVEntity $entity): void
    {
        $csv = CSV::find($id);
        if (!$csv instanceof CSV) {
            throw new NotFoundException('CSV not found');
        }

        DB::beginTransaction();
        try {
            CSV::find($id)->fill($entity->asArray())->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will create a CSV
     * @param int $id
     * @return int
     * @throws NotFoundException
     */
    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }
}
