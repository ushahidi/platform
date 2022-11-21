<?php

namespace Ushahidi\Modules\V5\Repository\User;

use Ushahidi\Modules\V5\Models\User;
use Ushahidi\Modules\V5\Repository\User\UserRepository as UserRepository;
use Ushahidi\Core\Exception\NotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\User as UserEntity;
use Ushahidi\Modules\V5\DTO\UserSearchFields;

class EloquentUserRepository implements UserRepository
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
    ): LengthAwarePaginator {
        return $this->setSearchCondition(
            $user_search_fields,
            User::take($limit)
                ->skip($skip)
                ->orderBy($sortBy, $order)
        )->paginate($limit);
    }

    /**
     * This method will fetch a single User from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return User
     * @throws NotFoundException
     */
    public function findById(int $id): User
    {
        $User = User::find($id);
        if (!$User instanceof User) {
            throw new NotFoundException('User not found');
        }
        return $User;
    }

    private function setSearchCondition(UserSearchFields $user_search_fields, $builder)
    {

        if ($user_search_fields->q()) {
            $builder->where('realname', 'LIKE', "%" . $user_search_fields->q() . "%");
        }
        if ($user_search_fields->role()) {
            $builder->whereIn('role', $user_search_fields->role());
        }
        return $builder;
    }

    /**
     * This method will create a User
     * @param UserEntity $user
     * @return int
     * @throws \Exception
     */
    public function create(UserEntity $user_entity): int
    {
        DB::beginTransaction();
        try {
            $User = User::create($user_entity->asArray());
            DB::commit();
            return $User->id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

     /**
     * This method will update the User
     * @param int @id
     * @param UserEntity $user_entity
     * @throws NotFoundException
     */
    public function update(int $id, UserEntity $user_entity): void
    {
        $User = User::find($id);
        if (!$User instanceof User) {
            throw new NotFoundException('User not found');
        }
        
        DB::beginTransaction();
        try {
            User::find($id)->fill($user_entity->asArray())->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

     /**
     * This method will create a User
     * @param int $id
     * @return int
     * @throws NotFoundException
     */
    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }
}
