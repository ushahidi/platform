<?php

namespace Ushahidi\Modules\V5\Repository\User;

use Ushahidi\Modules\V5\Models\UserSetting;
use Ushahidi\Modules\V5\Repository\User\UserSettingRepository ;
use Ushahidi\Core\Exception\NotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EloquentUserSettingRepository implements UserSettingRepository
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
    ): LengthAwarePaginator {
        return $this->setSearchCondition(
            $search_data,
            UserSetting::take($limit)
                ->skip($skip)
                ->orderBy($sortBy, $order)
        )->paginate($limit);
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

    private function setSearchCondition(array $search_data, $builder)
    {

        if ($search_data['q']) {
            $builder->where('name', 'LIKE', "%" . $search_data['q'] . "%");
        }
        if ($search_data['name']) {
            $builder->where('name', '=', $search_data['name']);
        }
        return $builder;
    }

    /**
     * This method will create a UserSetting
     * @param array $data
     * @return int
     * @throws \Exception
     */
    public function create(array $input): int
    {
        DB::beginTransaction();
        try {
            $UserSetting = UserSetting::create($input);
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
     * @param array $input
     * @throws NotFoundException
     */
    public function update(int $id, array $input): void
    {

        $UserSetting = UserSetting::find($id);
        if (!$UserSetting instanceof UserSetting) {
            throw new NotFoundException('UserSetting not found');
        }
        
        DB::beginTransaction();
        try {
            UserSetting::find($id)->fill($input)->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

     /**
     * This method will create a UserSetting
     * @param int $id
     * @return int
     * @throws NotFoundException
     */
    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }
}
