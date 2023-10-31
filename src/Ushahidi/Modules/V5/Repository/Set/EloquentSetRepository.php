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
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Tool\SearchData;

class EloquentSetRepository implements SetRepository
{
    /**
     * @var SearchData
     */
    protected $searchData;

    /**
     * This method will fetch all the Set for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     *
     * @return Set[]|LengthAwarePaginator
     */
    public function fetch()
    {
        $data = $this->searchData;

        $query = $this->setSearchCondition(Set::query(), $data);

        $sort = $data->getFilter('sort');
        $order = $data->getFilter('order');
        if (isset($sort)) {
            $query->orderBy($sort, $order);
        }

        if ($data->getFilter('with_post_count') == true) {
            $query->withCount('posts');
        }

        $limit = $data->getFilter('limit');
        if (isset($limit)) {
            return $query->paginate($limit);
        }
        return $query->get();
    }

    private function setSearchCondition(Builder $builder, SearchData $search_fields)
    {
        $is_saved_search = (int) $search_fields->getFilter('is_saved_search');
        $builder->where('search', '=', $is_saved_search);

        $keyword = $search_fields->getFilter('keyword');
        if (isset($keyword) && !empty($keyword)) {
            $builder->where('name', 'LIKE', "%" . $keyword . "%");
        }

        $is_admin = $search_fields->getFilter('is_admin');
        if ($is_admin == false) {
            // Default search for everyone and guest user
            $builder->where(function (Builder $query) {
                $query->whereNull('role');

                $query->orWhere('role', 'LIKE', "%everyone%");
            });

            // is owner
            $user_id = $search_fields->getFilter('user_id');
            if (isset($user_id) && !is_null($user_id)) {
                $builder->orWhere(function (Builder $query) use ($user_id) {
                    $query->where('role', 'LIKE', "%me%")
                        ->where('user_id', '=', $user_id);
                });
            }

            // is not admin and has role
            $role = $search_fields->getFilter('role');
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
     * @return Set
     * @throws NotFoundException
     */
    public function findById(int $id, bool $search = false): Set
    {
        $set = Set::where('id', '=', $id)->where('search', '=', $search)->first();
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
