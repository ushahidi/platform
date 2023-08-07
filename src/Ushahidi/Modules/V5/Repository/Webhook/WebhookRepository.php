<?php

namespace Ushahidi\Modules\V5\Repository\Webhook;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Webhook\Webhook;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\WebhookSearchFields;
use Ushahidi\Core\Entity\Webhook as WebhookEntity;

interface WebhookRepository
{

    /**
     * This method will fetch all the Webhook for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param WebhookSearchFields user_search_fields
     * @return LengthAwarePaginator
     */
    public function fetch(Paging $paging, WebhookSearchFields $search_fields): LengthAwarePaginator;

    /**
     * This method will fetch a single Webhook from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Webhook
     * @throws NotFoundException
     */
    public function findById(int $id): Webhook;

    /**
     * This method will create a Webhook
     * @param WebhookEntity $entity
     * @return int
     */
    public function create(WebhookEntity $entity): int;

    /**
     * This method will update the Webhook
     * @param int $id
     * @param WebhookEntity $entity
     */
    public function update(int $id, WebhookEntity $entity): void;

       /**
     * This method will delete the Webhook
     * @param int $id
     */
    public function delete(int $id): void;
}
