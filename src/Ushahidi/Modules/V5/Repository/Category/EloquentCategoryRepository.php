<?php

namespace Ushahidi\Modules\V5\Repository\Category;

use Illuminate\Database\Eloquent\Builder;
use Ushahidi\Modules\V5\Models\Category;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\CategorySearchFields;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Core\Tool\SearchData;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentCategoryRepository implements CategoryRepository
{
    /**
     * @var SearchData
     */
    protected $searchData = null;

    /**
     * Set search constraints
     *
     * @param SearchData $searchData
     * @return void
     */
    public function setSearchParams(SearchData $searchData)
    {
        $this->searchData = $searchData;
    }



    /**
     * This method will fetch a single Category from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @param array $fields
     * @param array $with
     * @return Category
     * @throws NotFoundException
     */
    public function findById(int $id, array $fields = [], array $with = []): Category
    {
        $query = Category::where('id', '=', $id);
        if (count($fields)) {
            $query->select($fields);
        }
        if (count($with)) {
            $query->with($with);
        }
        $category = $query->first();
        dd($category);

        if (!$category instanceof Category) {
            throw new NotFoundException('Category not found');
        }
        return $category;
    }


    private function addTableNamePrefix($fields)
    {
        $after_update = [];
        foreach ($fields as $field) {
            $after_update[] = 'tags.' . $field;
        }
        return $after_update;
    }

    
    private function setSearchCondition(CategorySearchFields $search_fields, $builder)
    {

        if ($search_fields->q()) {
            $builder->where('tag', 'LIKE', "%" . $search_fields->q() . "%");
        }

        if ($search_fields->tag()) {
            $builder->where('tag', '=', $search_fields->tag());
        }

        if ($search_fields->parentId()) {
            $builder->where('parent_id', '=', $search_fields->parentId());
        } elseif ($search_fields->isParent()) {
            $builder->whereNull('parent_id');
        }

        if ($search_fields->level()) {
            $builder->where('level', '=', $search_fields->level());
        }

        if ($search_fields->type()) {
            $builder->where('type', '=', $search_fields->type());
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

     /**
     * This method will fetch all the Set for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param Paging $limit
     * @param CategorySearchFields $search_fields
     * @param array $fields
     * @param array $with
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<Set>
     */
    public function paginate(
        Paging $paging,
        CategorySearchFields $search_fields,
        array $fields = [],
        array $with = []
    ): LengthAwarePaginator {
        $fields = $this->addTableNamePrefix($fields);
        // add the order field if not found
        if (!in_array('tags.'.$paging->getOrderBy(), $fields)) {
            $fields[] = 'tags.'.$paging->getOrderBy();
        }
        $query = Category::take($paging->getLimit())
            ->orderBy('tags.'.$paging->getOrderBy(), $paging->getOrder());

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


    public function store(
        ?string $parentId,
        ?int $userId,
        string $tag,
        string $slug,
        string $type,
        ?string $description,
        ?string $color,
        ?string $icon,
        int $priority,
        ?array $role,
        string $defaultBaseLanguage,
        array $availableLanguages
    ): int {
        $input = array_filter([
            'parent_id' => $parentId,
            'user_id' => $userId,
            'tag' => $tag,
            'slug' => $slug,
            'type' => $type,
            'description' => $description,
            'color' => $color,
            'icon' => $icon,
            'priority' => $priority,
            'role' => $role,
            'base_language' => $defaultBaseLanguage
        ], function ($element) {
            return !is_null($element);
        });
        $category = new Category($input);

        $isSaved = $category->saveOrFail();

        return $category->id;
    }

    public function slugExists(string $slug): bool
    {
        return Category::query()->where('slug', $slug)->exists();
    }

    public function update(
        int $id,
        ?string $parentId,
        ?int $userId,
        ?string $tag,
        ?string $slug,
        ?string $type,
        ?string $description,
        ?string $color,
        ?string $icon,
        ?int $priority,
        ?array $role,
        ?string $defaultBaseLanguage,
        ?array $availableLanguages
    ): int {
           $input = [
            'parent_id' => $parentId,
            //'user_id' => $userId,
            'tag' => $tag,
            'slug' => $slug,
            'type' => $type,
            'description' => $description,
            'color' => $color,
            'icon' => $icon,
            'priority' => $priority,
            'base_language' => $defaultBaseLanguage
           ];
           $category = $this->findById($id);
        // Ugly workaround for the role field being nullable
        // todo: make it better
           $category->role = $role;
           $category->fill($input);
           $category->saveOrFail();
           $category->refresh();

           return $category->id;
    }
}
