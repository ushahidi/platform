<?php

namespace Ushahidi\Modules\V5\Repository\Notification;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Notification;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\NotificationSearchFields;
use Ushahidi\Core\Entity\Notification as NotificationEntity;

interface NotificationRepository
{

    /**
     * This method will fetch all the Notification for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param NotificationSearchFields user_search_fields
     * @return LengthAwarePaginator
     */
    public function fetch(Paging $paging, NotificationSearchFields $search_fields): LengthAwarePaginator;

    /**
     * This method will fetch a single Notification from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Notification
     * @throws NotFoundException
     */
    public function findById(int $id): Notification;

    /**
     * This method will create a Notification
     */
    public function create(NotificationEntity $entity): int;

    /**
     * This method will update the Notification
     * @param int $id
     * @param NotificationEntity $entity
     */
    public function update(int $id, NotificationEntity $entity): void;

       /**
     * This method will delete the Notification
     * @param int $id
     */
    public function delete(int $id): void;
}
