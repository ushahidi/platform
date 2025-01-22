<?php

namespace Ushahidi\Modules\V5\Repository\Role;

use Ushahidi\Modules\V5\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Ushahidi\Core\Entity\Role as RoleEntity;
use Ushahidi\Modules\V5\DTO\RoleSearchFields;

interface RoleRepository
{
 /**
     * This method will fetch all the Role for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param RoleSearchFields role_search_fields
     * @return Role[]
     */
    public function fetch(
        int $limit,
        int $skip,
        string $sortBy,
        string $order,
        RoleSearchFields $role_search_fields
    ): LengthAwarePaginator;

    /**
     * This method will fetch a single Role from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Role
     * @throws NotFoundException
     */
    public function findById(int $id): Role;

    /**
     * @param string $role
     * @return Model|Role
     */
    public function findByRole(string $role): Role;
    
    /**
     * This method will create a Role
     * @param array $data
     * @return int
     */
    public function create(RoleEntity $entity): int;

    /**
     * This method will update the Role
     * @param int $id
     * @param array $data
     */
    public function update(int $id, RoleEntity $entity): void;

       /**
     * This method will delete the Role
     * @param int $id
     */
    public function delete(int $id): void;


    /**
     * This method will create a Role permission
     * @param array $data
     * @return int
     */
    public function createRolePermission(string $role, string $permission): int;


    
    /**
     * This method will delete the Role permission by role
     * @param int $id
     */
    public function deleteRolePermissionByRole(string $role): void;
}
