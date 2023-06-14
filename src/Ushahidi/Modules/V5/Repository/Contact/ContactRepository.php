<?php

namespace Ushahidi\Modules\V5\Repository\Contact;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Contact;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\ContactSearchFields;
use Ushahidi\Core\Entity\Contact as ContactEntity;

interface ContactRepository
{

    /**
     * This method will fetch all the Contact for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param ContactSearchFields user_search_fields
     * @return LengthAwarePaginator
     */
    public function fetch(Paging $paging, ContactSearchFields $search_fields): LengthAwarePaginator;

    /**
     * This method will fetch a single Contact from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Contact
     * @throws NotFoundException
     */
    public function findById(int $id): Contact;

    /**
     * This method will create a Contact
     * @param ContactEntity $entity
     * @return int
     */
    public function create(ContactEntity $entity): int;

    /**
     * This method will update the Contact
     * @param int $id
     * @param ContactEntity $entity
     */
    public function update(int $id, ContactEntity $entity): void;

       /**
     * This method will delete the Contact
     * @param int $id
     */
    public function delete(int $id): void;
}
