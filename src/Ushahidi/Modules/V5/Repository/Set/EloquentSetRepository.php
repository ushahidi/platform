<?php

namespace Ushahidi\Modules\V5\Repository\Set;

use Illuminate\Database\Eloquent\Builder;
use Ushahidi\Modules\V5\Models\Set;
use Ushahidi\Modules\V5\Repository\Set\SetRepository;
use Ushahidi\Core\Exception\NotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\DTO\CollectionSearchFields;
use Ushahidi\Core\Entity\Set as CollectionEntity;
use Ushahidi\Core\Tool\SearchData;
use Ushahidi\Modules\V5\DTO\Paging;

class EloquentSetRepository implements SetRepository
{
    /**
     * @var SearchData
     */
    protected $searchData;

    private function addTableNamePrefix($fields)
    {
        $after_update = [];
        foreach ($fields as $field) {
            $after_update[] = 'sets.' . $field;
        }
        return $after_update;
    }

    /**
     * This method will fetch all the Set for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param Paging $limit
     * @param CollectionSearchFields $search_fields
     * @param array $fields
     * @param array $with
     *
     * @return Set[]|LengthAwarePaginator
     */
    public function paginate(
        Paging $paging,
        CollectionSearchFields $search_fields,
        array $fields = [],
        array $with = []
    ): LengthAwarePaginator {
        $fields = $this->addTableNamePrefix($fields);
        // add the order field if not found
        if (!in_array('sets.'.$paging->getOrderBy(), $fields)) {
            $fields[] = 'sets.'.$paging->getOrderBy();
        }
        $query = Set::take($paging->getLimit())
            ->orderBy('sets.'.$paging->getOrderBy(), $paging->getOrder());

        $query = $this->setSearchCondition($search_fields, $query);

        if (count($fields)) {
            $query->select($fields);
        }
        if (count($with)) {
            $query->with($with);
        }
        $query->distinct();

        return $query->paginate($paging->getLimit());
    }
    private function setSearchCondition(CollectionSearchFields $search_fields, $builder)
    {
        $builder->where('search', '=', $search_fields->isSavedSearch());

        if ($search_fields->q()) {
            $builder->where('name', 'LIKE', "%" . $search_fields->q() . "%");
        }
        
        if (!$search_fields->isAdmin()) {
            // Default search for everyone and guest user
            $builder->where(function (Builder $query) {
                $query->whereNull('role');

                $query->orWhere('role', 'LIKE', "%everyone%");
            });

            // is owner
            $user_id = $search_fields->userID();
            if (isset($user_id) && !is_null($user_id)) {
                $builder->orWhere(function (Builder $query) use ($user_id) {
                    $query->where('role', 'LIKE', "%me%")
                        ->where('user_id', '=', $user_id);
                });
            }

            // is not admin and has role
            $role = $search_fields->role();
            if (isset($role) && !is_null($role)) {
                $builder->orWhere(function (Builder $query) use ($role) {
                    $query->where('role', 'LIKE', "%" . $role . "%");
                });
            }
        }

        return $builder;
    }

    public function setSearchParams(SearchData $searchData)
    {
        $this->searchData = $searchData;
    }

    /**
     * This method will fetch a single Set from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @param bool $search
     * @param array fields
     * @param array with
     * @return Set
     * @throws NotFoundException
     */
    public function findById(int $id, bool $search = false, array $fields = [], array $with = []): Set
    {
        $query = Set::where('id', '=', $id)->where('search', '=', $search);
        if (count($fields)) {
            $query->select($fields);
        }
        if (count($with)) {
            $query->with($with);
        }
        $set = $query->first();
        if (!$set instanceof Set) {
            throw new NotFoundException('set not found');
        }
        return $set;
    }

    /**
     * This method will create a Set
     * @param CollectionEntity $data
     * @return int
     * @throws \Exception
     */
    public function create(CollectionEntity $input): int
    {
        DB::beginTransaction();
        try {
            $set = set::create($input->asArray());
            DB::commit();
            return $set->id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will update the Set
     * @param int $id
     * @param CollectionEntity $set_entity
     */


    public function update(int $id, CollectionEntity $set_entity, bool $search = false): void
    {
        $set = Set::find($id);
        if (!$set instanceof Set) {
            throw new NotFoundException('Set not found');
        }

        DB::beginTransaction();
        try {
            Set::find($id)->fill($set_entity->asArray())->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will create a Survey
     * @param int $id
     * @throws NotFoundException
     */
    public function delete(int $id, bool $search = false): void
    {
        $this->findById($id, $search)->delete();
    }
}
