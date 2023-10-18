<?php

namespace Ushahidi\Modules\V5\Repository\Category;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Category;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\CategorySearchFields;
use Ushahidi\Core\Exception\NotFoundException;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Tool\SearchData;

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

    private function setSearchCondition(Builder $builder, ?SearchData $search_fields)
    {
        if ($search_fields === null) {
            return $builder;
        }

        $parent_id = $search_fields->getFilter('parent_id');
        $is_parent = $search_fields->getFilter('is_parent');

        if (isset($parent_id)) {
            $builder->where('parent_id', $parent_id);
        } elseif ($is_parent === false) {
            $builder->whereNull('parent_id');
        }

        $builder->where(function (Builder $builder) use ($search_fields) {
            $keyword = $search_fields->getFilter('keyword');
            $tag = $search_fields->getFilter('tag');
            $type = $search_fields->getFilter('type');

            if (isset($keyword)) {
                $builder->where('tag', 'LIKE', "%" . $keyword . "%");
            }

            if (isset($tag)) {
                $builder->orWhere('tag', 'LIKE', "%" . $keyword . "%");
            }

            if (isset($type)) {
                $builder->orWhere('type', '=', $type);
            }
        });

        $is_admin = $search_fields->getFilter('is_admin');
        if ($is_admin === false) {
            $builder->where(function (Builder $builder) use ($search_fields) {
                // Default always get categories with null roles or has everyone
                $builder->whereNull('role');

                // This query isn't working as expected
                $builder->orWhere('role', 'like', '%everyone%');

                $role = $search_fields->getFilter('role');
                if (isset($role) && !is_null($role)) {
                    $builder->orWhere('role', 'like', "%" . $role . "%");
                }

                // If it's a logged in user
                $user_id = $search_fields->getFilter('user_id');
                if (isset($user_id) && !is_null($user_id)) {
                    // Where the user is the owner of the category
                    $builder->orWhere(function (Builder $query) use ($user_id) {
                        //TODO: Fix this query in future release
                        $query->where('role', 'like', '%me%')
                            ->where('user_id', $user_id);
                    });
                }

            });
        }

        // var_dump($builder->toSql());
        // exit;
        return $builder;
    }

    /**
     * This method will fetch a single Category from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Category
     * @throws NotFoundException
     */
    public function findById(int $id): Category
    {
        $category = Category::find($id);
        if (!$category instanceof Category) {
            throw new NotFoundException('Category not found');
        }
        return $category;
    }

    public function fetchAll(Paging $paging)
    {
        return $this->setSearchCondition(
            Category::take($paging->getLimit())
                ->skip($paging->getSkip())
                ->orderBy($paging->getOrderBy(), $paging->getOrder()),
            $this->searchData
        )->paginate($paging->getLimit());
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
            'user_id' => $userId,
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
