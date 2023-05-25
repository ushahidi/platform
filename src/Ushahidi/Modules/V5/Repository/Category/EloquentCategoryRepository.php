<?php

namespace Ushahidi\Modules\V5\Repository\Category;

use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Category;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\CategorySearchFields;
use Ushahidi\Core\Exception\NotFoundException;
use Illuminate\Support\Facades\Auth;

class EloquentCategoryRepository implements CategoryRepository
{
    private function setSearchCondition(CategorySearchFields $search_fields, $builder)
    {

        if ($search_fields->q()) {
            $builder->where('tag', 'LIKE', "%" . $search_fields->q() . "%");
        }
        if ($search_fields->tag()) {
            $builder->where('tag', '=', $search_fields->tag());
        }
        if ($search_fields->type()) {
            $builder->where('type', '=', $search_fields->type());
        }

        if ($search_fields->level() === 'parent') {
            $builder->whereNull('parent_id');
        }
        if ($search_fields->parentId()) {
            $builder->where('parent_id', '=', $search_fields->parentId());
        }

        if (!Auth::user() || !Auth::user()->id) {
            $builder->whereNull('role');
        } elseif ($search_fields->role() && $search_fields->role() != "admin") {
            $builder->where(function ($query) use ($search_fields) {
                $query->whereNull('role')
                    ->orWhere('role', 'LIKE', "%" . $search_fields->role() . "%");
            });
        }
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
    public function fetchAll(Paging $paging, CategorySearchFields $category_search_fields)
    {
        return $this->setSearchCondition(
            $category_search_fields,
            Category::take($paging->getLimit())
                ->skip($paging->getSkip())
                ->orderBy($paging->getOrderBy(), $paging->getOrder())
        )->paginate($paging->getLimit());
    }

    public function store(
        ?string $parentId,
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

        $category->saveOrFail();
        $category->refresh();

        return $category->id;
    }

    public function slugExists(string $slug): bool
    {
        return Category::query()->where('slug', $slug)->exists();
    }

    public function update(
        int $id,
        ?string $parentId,
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
        $input = array_filter([
            'parent_id' => $parentId,
            'tag' => $tag,
            'slug' => $slug,
            'type' => $type,
            'description' => $description,
            'color' => $color,
            'icon' => $icon,
            'priority' => $priority,
            'base_language' => $defaultBaseLanguage
        ], function ($element) {
            return !is_null($element);
        });
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
