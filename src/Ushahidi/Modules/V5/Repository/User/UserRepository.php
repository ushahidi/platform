<?php

namespace Ushahidi\Modules\V5\Repository\User;

use Ushahidi\Modules\V5\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Ushahidi\Core\Entity\User as UserEntity;
use Ushahidi\Modules\V5\DTO\UserSearchFields;

interface UserRepository
{
 /**
     * This method will fetch all the User for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param UserSearchFields user_search_fields
     * @return User[]
     */
    public function fetch(
        int $limit,
        int $skip,
        string $sortBy,
        string $order,
        UserSearchFields $user_search_fields
    ): LengthAwarePaginator;

    /**
     * This method will fetch a single User from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return User
     * @throws NotFoundException
     */
    public function findById(int $id): User;

    /**
     * This method will fetch a single User from the database utilising
     * Laravel Eloquent ORM.
     * @param string $email
     * @return User
     */
    public function findByEmail(string $email): ?User;

   

    /**
     * This method will create a User
     * @param UserEntity $entity
     * @return int
     */
    public function create(UserEntity $entity): int;

    /**
     * This method will update the User
     * @param int $id
     * @param UserEntity $entity
     */
    public function update(int $id, UserEntity $entity): void;

       /**
     * This method will delete the User
     * @param int $id
     */
    public function delete(int $id): void;

    /**
     * This method will create a token to reset the password.
     * @param int $id
     * @return User
     */
    public function getResetToken(int $user_id);

    public function isValidResetToken($token): bool;

    public function setPassword($token, $password);
    
    public function deleteResetToken($token);
}
