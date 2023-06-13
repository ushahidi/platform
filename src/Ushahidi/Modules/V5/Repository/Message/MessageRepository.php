<?php

namespace Ushahidi\Modules\V5\Repository\Message;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Message;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\MessageSearchFields;
use Ushahidi\Core\Entity\Message as MessageEntity;

interface MessageRepository
{

    /**
     * This method will fetch all the Message for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param MessageSearchFields user_search_fields
     * @return LengthAwarePaginator
     */
    public function fetch(Paging $paging, MessageSearchFields $search_fields): LengthAwarePaginator;

    /**
     * This method will fetch a single Message from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Message
     * @throws NotFoundException
     */
    public function findById(int $id): Message;

    /**
     * This method will create a Message
     * @param MessageEntity $entity
     * @return int
     */
    public function create(MessageEntity $entity): int;

    /**
     * This method will update the Message
     * @param int $id
     * @param MessageEntity $entity
     */
    public function update(int $id, MessageEntity $entity): void;

       /**
     * This method will delete the Message
     * @param int $id
     */
    public function delete(int $id): void;
}
