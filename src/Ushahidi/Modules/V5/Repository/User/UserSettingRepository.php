<?php

namespace Ushahidi\Modules\V5\Repository\User;

use Ushahidi\Modules\V5\Models\UserSetting;
use Illuminate\Pagination\LengthAwarePaginator;
use Ushahidi\Core\Entity\UserSetting as UserSettingEntity;

interface UserSettingRepository
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
    ): LengthAwarePaginator;

    /**
     * This method will fetch a single UserSetting from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return UserSetting
     * @throws NotFoundException
     */
    public function findById(int $id): UserSetting;

    /**
     * This method will create a UserSetting
     * @param UserSettingEntity $data
     * @return int
     */
    public function create(UserSettingEntity $entity): int;

    /**
     * This method will update the UserSetting
     * @param int $id
     * @param UserSettingEntity $data
     */
    public function update(int $id, UserSettingEntity $entity): void;

    /**
     * This method will delete the UserSetting
     * @param int $id
     * @param int $user_id
     */
    public function delete(int $id, int $user_id): void;
}
