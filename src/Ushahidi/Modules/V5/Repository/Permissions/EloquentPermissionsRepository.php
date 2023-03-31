<?php

namespace Ushahidi\Modules\V5\Repository\Permissions;

use Ushahidi\Modules\V5\Models\Permissions;
use Ushahidi\Modules\V5\Repository\Permissions\PermissionsRepository as PermissionsRepository;
use Ushahidi\Core\Exception\NotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentPermissionsRepository implements PermissionsRepository
{
    /**
     * This method will fetch all the Permissions for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @return Permissions[]
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
            Permissions::take($limit)
                ->skip($skip)
                ->orderBy($sortBy, $order)
        )->paginate($limit ? $limit : config('paging.default_laravel_pageing_limit'));
    }

    /**
     * This method will fetch a single Permissions from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Permissions
     * @throws NotFoundException
     */
    public function findById(int $id): Permissions
    {
        $permissions = Permissions::find($id);
        if (!$permissions instanceof Permissions) {
            throw new NotFoundException('permissions not found');
        }
        return $permissions;
    }

    private function setSearchCondition(array $search_data, $builder)
    {

        if ($search_data['q']) {
            $builder->where('name', 'LIKE', "%" . $search_data['q'] . "%");
        }
        return $builder;
    }
}
