<?php

namespace Ushahidi\Modules\V5\Repository\User;

use Ushahidi\Modules\V5\Models\UserSetting;
use Ushahidi\Modules\V5\Repository\User\UserSettingRepository;
use Ushahidi\Core\Exception\NotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\UserSetting as UserSettingEntity;

class EloquentUserSettingRepository implements UserSettingRepository
{
    /**
     * This method will fetch all the UserSetting for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $user_id
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @return UserSetting[]
     */
    public function fetch(
        int $user_id,
        int $limit,
        int $skip,
        string $sortBy,
        string $order
    ): LengthAwarePaginator {
        return UserSetting::take($limit)
            ->skip($skip)
            ->orderBy($sortBy, $order)
            ->where('user_id', '=', $user_id)
            ->paginate($limit ? $limit : config('paging.default_laravel_pageing_limit'));
    }

    /**
     * This method will fetch a single UserSetting from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return UserSetting
     * @throws NotFoundException
     */
    public function findById(int $id): UserSetting
    {
        $UserSetting = UserSetting::find($id);
        if (!$UserSetting instanceof UserSetting) {
            throw new NotFoundException('UserSetting not found');
        }
        return $UserSetting;
    }
    /**
     * This method will create a UserSetting
     * @param UserSettingEntity $data
     * @return int
     * @throws \Exception
     */
    public function create(UserSettingEntity $entity): int
    {
        DB::beginTransaction();
        try {
            $UserSetting = UserSetting::create($entity->asArray());
            DB::commit();
            return $UserSetting->id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will update the UserSetting
     * @param int @id
     * @param UserSettingEntity $entity
     * @throws NotFoundException
     */
    public function update(int $id, UserSettingEntity $entity): void
    {

        $UserSetting = UserSetting::find($id);
        if (!$UserSetting instanceof UserSetting) {
            throw new NotFoundException('UserSetting not found');
        }

        DB::beginTransaction();
        try {
            UserSetting::find($id)->fill($entity->asArray())->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will create a UserSetting
     * @param int $id
     * @param int $user_id
     * @return int
     * @throws NotFoundException
     */
    public function delete(int $id, int $user_id): void
    {
        UserSetting::where('user_id', "=", $user_id)->where('id', "=", $id)->delete();
    }
}
