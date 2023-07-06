<?php

namespace Ushahidi\Modules\V5\Repository\Message;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\Message;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\MessageSearchFields;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\Message as MessageEntity;
use Ushahidi\Core\Tool\Tile;

class EloquentMessageRepository implements MessageRepository
{
    private function setSearchCondition(MessageSearchFields $search_fields, $builder)
    {
        if ($search_fields->q()) {
            $builder->where(function ($query) use ($search_fields) {
                 $query->where("messages.title", "like", "%" . $search_fields->q() . "%");
                 $query->orWhere("messages.message", "like", "%" . $search_fields->q() . "%");
            });
                // $query->whereRaw(
                //     '(messages.title like ? OR messages.content like ?)',
                //     ["%" . $search_fields->q() . "%", "%" . $search_fields->q() . "%"]
                // );
        }
        if ($search_fields->contact()) {
            $builder->whereIn('messages.contact_id', $search_fields->contact());
        }


        if ($search_fields->box() === 'outbox') {
            $builder->whereIn('messages.direction', ["outgoing"]);
        } elseif ($search_fields->box() === 'inbox') {
            $builder->whereIn('messages.direction', ["incoming"]);
        }
       
        if ($search_fields->box() === 'archived') {
            $builder->where('messages.status', '=', "archived");
        } elseif ($search_fields->status()) {
            if ($search_fields->status() !== 'all') {
                // Search for a specific status
                $builder->where('messages.status', '=', $search_fields->status());
            }
        } else {
            $builder->where('messages.status', '!=', "archived");
        }
 
        if ($search_fields->parent()) {
            $builder->whereIn('messages.parent_id', $search_fields->parent());
        }

        if ($search_fields->contact()) {
            $builder->whereIn('messages.contact_id', $search_fields->contact());
        }

        if ($search_fields->type()) {
            $builder->where('messages.type', '=', $search_fields->type());
        }

        if ($search_fields->dataSource()) {
            $builder->where('messages.data_source', '=', $search_fields->dataSource());
        }
        return $builder;
    }
    /**
     * This method will fetch all the Message for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param MessageSearchFields user_search_fields
     * @return Message[]
     */
    public function fetch(Paging $paging, MessageSearchFields $search_fields): LengthAwarePaginator
    {
        return $this->setSearchCondition(
            $search_fields,
            Message::take($paging->getLimit())
                ->skip($paging->getSkip())
                ->orderBy($paging->getOrderBy(), $paging->getOrder())
        )->paginate($paging->getLimit() ? $paging->getLimit() : config('paging.default_laravel_pageing_limit'));
    }

    /**
     * This method will fetch a single Message from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Message
     * @throws NotFoundException
     */
    public function findById(int $id): Message
    {
        $message = Message::find($id);
        if (!$message instanceof Message) {
            throw new NotFoundException('Message not found');
        }
        return $message;
    }


    /**
     * This method will create a Message
     * @param MessageEntity $entity
     * @return int
     * @throws \Exception
     */
    public function create(MessageEntity $entity): int
    {
        DB::beginTransaction();
        try {
            $message = Message::create($entity->asArray());
            DB::commit();
            return $message->id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will update the Message
     * @param int @id
     * @param MessageEntity $entity
     * @throws NotFoundException
     */
    public function update(int $id, MessageEntity $entity): void
    {
        $message = Message::find($id);
        if (!$message instanceof Message) {
            throw new NotFoundException('Message not found');
        }

        DB::beginTransaction();
        try {
            Message::find($id)->fill($entity->asArray())->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will create a Message
     * @param int $id
     * @return int
     * @throws NotFoundException
     */
    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }
}
