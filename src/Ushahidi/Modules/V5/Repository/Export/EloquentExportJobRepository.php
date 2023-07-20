<?php

namespace Ushahidi\Modules\V5\Repository\Export;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\ExportJob;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\ExportJobSearchFields;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\ExportJob as ExportJobEntity;

class EloquentExportJobRepository implements ExportJobRepository
{
    private function setSearchCondition(ExportJobSearchFields $search_fields, $builder)
    {
        if (count($search_fields->user())) {
            $builder->whereIn('export_job.user_id', $search_fields->user());
        }

        if ($search_fields->maxExpiration()) {
            $builder->where(function ($query) use ($search_fields) {
                $query->where("export_job.url_expiration", '>', intval($search_fields->maxExpiration()));
                $query->or_where("export_job.url_expiration", 'IS', null);
                $query->or_where("export_job.url_expiration", '=', 0);
            });
        }

        if ($search_fields->entityType()) {
            $builder->where('export_job.entity_type', '=', $search_fields->entityType());
        }
        return $builder;
    }
    /**
     * This method will fetch all the ExportJob for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param ExportJobSearchFields user_search_fields
     * @return ExportJob[]
     */
    public function fetch(Paging $paging, ExportJobSearchFields $search_fields): LengthAwarePaginator
    {
        return $this->setSearchCondition(
            $search_fields,
            ExportJob::take($paging->getLimit())
                ->skip($paging->getSkip())
                ->orderBy($paging->getOrderBy(), $paging->getOrder())
        )->paginate($paging->getLimit() ? $paging->getLimit() : config('paging.default_laravel_pageing_limit'));
    }

    /**
     * This method will fetch a single ExportJob from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return ExportJob
     * @throws NotFoundException
     */
    public function findById(int $id): ExportJob
    {
        $export_job = ExportJob::find($id);
        if (!$export_job instanceof ExportJob) {
            throw new NotFoundException('ExportJob not found');
        }
        return $export_job;
    }


    /**
     * This method will create a ExportJob
     * @param ExportJobEntity $entity
     * @return int
     * @throws \Exception
     */
    public function create(ExportJobEntity $entity): int
    {
        DB::beginTransaction();
        try {
            $export_job = ExportJob::create($entity->asArray());
            DB::commit();
            return $export_job->id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will update the ExportJob
     * @param int @id
     * @param ExportJobEntity $entity
     * @throws NotFoundException
     */
    public function update(int $id, ExportJobEntity $entity): void
    {
        $export_job = ExportJob::find($id);
        if (!$export_job instanceof ExportJob) {
            throw new NotFoundException('ExportJob not found');
        }

        DB::beginTransaction();
        try {
            ExportJob::find($id)->fill($entity->asArray())->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will create a ExportJob
     * @param int $id
     * @return int
     * @throws NotFoundException
     */
    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }
}
