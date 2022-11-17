<?php

namespace Ushahidi\Modules\V5\Repository\User;

use Ushahidi\Modules\V5\Models\UserSetting;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserSettingRepository
{
 /**
     * This method will fetch all the UserSetting for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @return UserSetting[]
     */
    public function fetch(
        int $limit,
        int $skip,
        string $sortBy,
        string $order,
        array $search_data
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
     * @param array $data
     * @return int
     */
    public function create(array $data): int;

    /**
     * This method will update the UserSetting
     * @param int $id
     * @param array $data
     */
    public function update(int $id, array $data): void;

       /**
     * This method will delete the UserSetting
     * @param int $id
     */
    public function delete(int $id): void;
}
