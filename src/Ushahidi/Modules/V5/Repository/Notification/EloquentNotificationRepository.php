<?php

namespace Ushahidi\Modules\V5\Repository\Notification;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\Notification;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\NotificationSearchFields;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\Notification as NotificationEntity;

class EloquentNotificationRepository implements NotificationRepository
{
    private function setSearchCondition(NotificationSearchFields $search_fields, $builder)
    {
        if (count($search_fields->user())) {
            $builder->whereIn('notifications.user_id', $search_fields->user());
        }

        if (count($search_fields->set())) {
            $builder->whereIn('notifications.set_id', $search_fields->set());
        }

        return $builder;
    }
    /**
     * This method will fetch all the Notification for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param NotificationSearchFields user_search_fields
     * @return Notification[]
     */
    public function fetch(Paging $paging, NotificationSearchFields $search_fields): LengthAwarePaginator
    {
        return $this->setSearchCondition(
            $search_fields,
            Notification::take($paging->getLimit())
                ->skip($paging->getSkip())
                ->orderBy($paging->getOrderBy(), $paging->getOrder())
        )->paginate($paging->getLimit() ? $paging->getLimit() : config('paging.default_laravel_pageing_limit'));
    }

    /**
     * This method will fetch a single Notification from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Notification
     * @throws NotFoundException
     */
    public function findById(int $id): Notification
    {
        $notification = Notification::find($id);
        if (!$notification instanceof Notification) {
            throw new NotFoundException('Notification not found');
        }
        return $notification;
    }


    /**
     * This method will create a Notification
     * @param NotificationEntity $entity
     * @return int
     * @throws \Exception
     */
    public function create(NotificationEntity $entity): int
    {
        DB::beginTransaction();
        try {
            $notification = Notification::create($entity->asArray());
            DB::commit();
            return $notification->id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will update the Notification
     * @param int @id
     * @param NotificationEntity $entity
     * @throws NotFoundException
     */
    public function update(int $id, NotificationEntity $entity): void
    {
        $notification = Notification::find($id);
        if (!$notification instanceof Notification) {
            throw new NotFoundException('Notification not found');
        }

        DB::beginTransaction();
        try {
            Notification::find($id)->fill($entity->asArray())->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will create a Notification
     * @param int $id
     * @return int
     * @throws NotFoundException
     */
    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }
}
