<?php

namespace Ushahidi\Modules\V5\Repository\User;

use Ushahidi\Modules\V5\Models\User;
use Ushahidi\Modules\V5\Repository\User\UserRepository as UserRepository;
use Ushahidi\Core\Exception\NotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EloquentUserRepository implements UserRepository
{
    /**
     * This method will fetch all the User for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @return User[]
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
     * This method will create a User
     * @param array $data
     * @return int
     * @throws \Exception
     */
    public function create(array $input): int
    {
        DB::beginTransaction();
        try {
            $User = User::create($input);
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
     * @param array $input
     * @throws NotFoundException
     */
    public function update(int $id, array $input): void
    {

        $User = User::find($id);
        if (!$User instanceof User) {
            throw new NotFoundException('User not found');
        }
        
        DB::beginTransaction();
        try {
            User::find($id)->fill($input)->save();
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
