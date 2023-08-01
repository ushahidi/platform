<?php

namespace Ushahidi\Modules\V5\Repository\Media;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\Media;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\MediaSearchFields;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\Media as MediaEntity;

class EloquentMediaRepository implements MediaRepository
{
    private function setSearchCondition(MediaSearchFields $search_fields, $builder)
    {
        if (count($search_fields->user())) {
            $builder->whereIn('medias.user_id', $search_fields->user());
        }

        if ($search_fields->type()) {
            $builder->where('medias.type', '=', $search_fields->type());
        }

        if ($search_fields->media()) {
            $builder->where('medias.media', '=', $search_fields->media());
        }

        if ($search_fields->dataSource()) {
            $builder->where('medias.data_source', '=', $search_fields->dataSource());
        }
        return $builder;
    }
    /**
     * This method will fetch all the Media for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param MediaSearchFields user_search_fields
     * @return Media[]
     */
    public function fetch(Paging $paging, MediaSearchFields $search_fields): LengthAwarePaginator
    {
        return $this->setSearchCondition(
            $search_fields,
            Media::take($paging->getLimit())
                ->skip($paging->getSkip())
                ->orderBy($paging->getOrderBy(), $paging->getOrder())
        )->paginate($paging->getLimit() ? $paging->getLimit() : config('paging.default_laravel_pageing_limit'));
    }

    /**
     * This method will fetch a single Media from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Media
     * @throws NotFoundException
     */
    public function findById(int $id): Media
    {
        $media = Media::find($id);
        if (!$media instanceof Media) {
            throw new NotFoundException('Media not found');
        }
        return $media;
    }


    /**
     * This method will create a Media
     * @param MediaEntity $entity
     * @return int
     * @throws \Exception
     */
    public function create(MediaEntity $entity): int
    {
        DB::beginTransaction();
        try {
            $media = Media::create($entity->asArray());
            DB::commit();
            return $media->id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will update the Media
     * @param int @id
     * @param MediaEntity $entity
     * @throws NotFoundException
     */
    public function update(int $id, MediaEntity $entity): void
    {
        $media = Media::find($id);
        if (!$media instanceof Media) {
            throw new NotFoundException('Media not found');
        }

        DB::beginTransaction();
        try {
            Media::find($id)->fill($entity->asArray())->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will create a Media
     * @param int $id
     * @return int
     * @throws NotFoundException
     */
    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }
}
