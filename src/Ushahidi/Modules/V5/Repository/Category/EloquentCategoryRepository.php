<?php

namespace Ushahidi\Modules\V5\Repository\Category;

use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Category;

class EloquentCategoryRepository implements CategoryRepository
{
    public function fetchByIdOrFail(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function fetchAll(): Collection
    {
        return Category::all();
    }

    public function store(
        ?string $parentId,
        string  $tag,
        string  $slug,
        string  $type,
        ?string $description,
        ?string $color,
        ?string $icon,
        int     $priority,
        ?array  $role,
        string  $defaultBaseLanguage,
        array   $availableLanguages
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
        $category = Category::findOrFail($id);
        // Ugly workaround for the role field being nullable
        // todo: make it better
        $category->role = $role;
        $category->fill($input);
        $category->saveOrFail();
        $category->refresh();

        return $category->id;
    }
}
