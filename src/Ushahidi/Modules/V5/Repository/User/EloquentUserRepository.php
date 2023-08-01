<?php

namespace Ushahidi\Modules\V5\Repository\User;

use Ushahidi\Modules\V5\Models\User;
use Ushahidi\Modules\V5\Repository\User\UserRepository as UserRepository;
use Ushahidi\Core\Exception\NotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\User as UserEntity;
use Ushahidi\Modules\V5\DTO\UserSearchFields;
use Ushahidi\Modules\V5\Models\UserResettoken;

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
        )->paginate($limit ? $limit : config('paging.default_laravel_pageing_limit'));
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



    /**
     * This method will fetch a single User from the database utilising
     * Laravel Eloquent ORM.
     * @param string $email
     * @return User
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', '=', $email)->first();
    }

    private function setSearchCondition(UserSearchFields $user_search_fields, $builder)
    {
        $builder->where(function ($query) use ($user_search_fields) {
            $query->where("users.realname", "like", "%" . $user_search_fields->q() . "%");
            $query->orWhere("users.email", "like", "%" . $user_search_fields->q() . "%");
        });


        if ($user_search_fields->role()) {
            $builder->whereIn('role', $user_search_fields->role());
        }
        return $builder;
    }

    /**
     * This method will create a User
     * @param UserEntity $entity
     * @return int
     * @throws \Exception
     */
    public function create(UserEntity $entity): int
    {
        DB::beginTransaction();
        try {
            $User = User::create($entity->asArray());
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
     * @param UserEntity $entity
     * @throws NotFoundException
     */
    public function update(int $id, UserEntity $entity): void
    {
        $User = User::find($id);
        if (!$User instanceof User) {
            throw new NotFoundException('User not found');
        }

        DB::beginTransaction();
        try {
            User::find($id)->fill($entity->asArray())->save();
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


    /**
     * This method will create a token to reset the password.
     * @param int $id
     * @return User
     */
    public function getResetToken(int $user_id)
    {
        $token = sprintf('%06X', mt_rand(0, 16777215));

        $input = [
            'reset_token' => $token,
            'user_id' => $user_id,
            'created' => time()
        ];
        // Question do we need to delete the old token ?!
        //UserResettoken::where("user_id",$user_id)->delete();
        
        // Save the token
        $result =  UserResettoken::create($input);
        return $token;
    }

    // ResetPasswordRepository
    public function isValidResetToken($token): bool
    {
        $count = UserResettoken::select(['user_id'])
            ->where('reset_token', '=', $token)
            ->where('created', '>', time() - 1800) // Expire tokens after less than 30 mins
            ->count();
        return $count !== 0;
    }

    // ResetPasswordRepository
    public function setPassword($token, $hashed_password)
    {
        $user_reset_token = UserResettoken::select(['user_id'])
            ->where('reset_token', '=', $token)->first();

        $user = User::find($user_reset_token->user_id);
        $user->update(["password" => $hashed_password]);
    }

    // ResetPasswordRepository
    public function deleteResetToken($token)
    {
        UserResettoken::select(['user_id'])
            ->where('reset_token', '=', $token)->delete();
    }
}
