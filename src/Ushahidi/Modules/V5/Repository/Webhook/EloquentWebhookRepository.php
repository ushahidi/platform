<?php

namespace Ushahidi\Modules\V5\Repository\Webhook;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\Webhook\Webhook;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\WebhookSearchFields;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\Webhook as WebhookEntity;

class EloquentWebhookRepository implements WebhookRepository
{
    private function setSearchCondition(WebhookSearchFields $search_fields, $builder)
    {
        if (count($search_fields->user())) {
            $builder->whereIn('webhooks.user_id', $search_fields->user());
        }

        return $builder;
    }
    /**
     * This method will fetch all the Webhook for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param WebhookSearchFields user_search_fields
     * @return Webhook[]
     */
    public function fetch(Paging $paging, WebhookSearchFields $search_fields): LengthAwarePaginator
    {
        return $this->setSearchCondition(
            $search_fields,
            Webhook::take($paging->getLimit())
                ->skip($paging->getSkip())
                ->orderBy($paging->getOrderBy(), $paging->getOrder())
        )->paginate($paging->getLimit() ? $paging->getLimit() : config('paging.default_laravel_pageing_limit'));
    }

    /**
     * This method will fetch a single Webhook from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Webhook
     * @throws NotFoundException
     */
    public function findById(int $id): Webhook
    {
        $webhook = Webhook::find($id);
        if (!$webhook instanceof Webhook) {
            throw new NotFoundException('Webhook not found');
        }
        return $webhook;
    }


    /**
     * This method will create a Webhook
     * @param WebhookEntity $entity
     * @return int
     * @throws \Exception
     */
    public function create(WebhookEntity $entity): int
    {
        DB::beginTransaction();
        try {
            $webhook = Webhook::create($entity->asArray());
            DB::commit();
            return $webhook->id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will update the Webhook
     * @param int @id
     * @param WebhookEntity $entity
     * @throws NotFoundException
     */
    public function update(int $id, WebhookEntity $entity): void
    {
        $webhook = Webhook::find($id);
        if (!$webhook instanceof Webhook) {
            throw new NotFoundException('Webhook not found');
        }

        DB::beginTransaction();
        try {
            Webhook::find($id)->fill($entity->asArray())->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will create a Webhook
     * @param int $id
     * @return int
     * @throws NotFoundException
     */
    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }
}
