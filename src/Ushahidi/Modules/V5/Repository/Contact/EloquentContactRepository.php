<?php

namespace Ushahidi\Modules\V5\Repository\Contact;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\Contact;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\ContactSearchFields;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\Contact as ContactEntity;

class EloquentContactRepository implements ContactRepository
{
    private function setSearchCondition(ContactSearchFields $search_fields, $builder)
    {
        if (count($search_fields->user())) {
            $builder->whereIn('contacts.user_id', $search_fields->user());
        }

        if ($search_fields->type()) {
            $builder->where('contacts.type', '=', $search_fields->type());
        }

        if ($search_fields->contact()) {
            $builder->where('contacts.contact', '=', $search_fields->contact());
        }

        if ($search_fields->dataSource()) {
            $builder->where('contacts.data_source', '=', $search_fields->dataSource());
        }
        return $builder;
    }
    /**
     * This method will fetch all the Contact for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param ContactSearchFields user_search_fields
     * @return Contact[]
     */
    public function fetch(Paging $paging, ContactSearchFields $search_fields): LengthAwarePaginator
    {
        return $this->setSearchCondition(
            $search_fields,
            Contact::take($paging->getLimit())
                ->skip($paging->getSkip())
                ->orderBy($paging->getOrderBy(), $paging->getOrder())
        )->paginate($paging->getLimit() ? $paging->getLimit() : config('paging.default_laravel_pageing_limit'));
    }

    /**
     * This method will fetch a single Contact from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Contact
     * @throws NotFoundException
     */
    public function findById(int $id): Contact
    {
        $contact = Contact::find($id);
        if (!$contact instanceof Contact) {
            throw new NotFoundException('Contact not found');
        }
        return $contact;
    }


    /**
     * This method will create a Contact
     * @param ContactEntity $entity
     * @return int
     * @throws \Exception
     */
    public function create(ContactEntity $entity): int
    {
        DB::beginTransaction();
        try {
            $contact = Contact::create($entity->asArray());
            DB::commit();
            return $contact->id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will update the Contact
     * @param int @id
     * @param ContactEntity $entity
     * @throws NotFoundException
     */
    public function update(int $id, ContactEntity $entity): void
    {
        $contact = Contact::find($id);
        if (!$contact instanceof Contact) {
            throw new NotFoundException('Contact not found');
        }

        DB::beginTransaction();
        try {
            Contact::find($id)->fill($entity->asArray())->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will create a Contact
     * @param int $id
     * @return int
     * @throws NotFoundException
     */
    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }
}
