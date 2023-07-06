<?php

namespace Ushahidi\Modules\V5\Repository\Category;

use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Category;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\CategorySearchFields;

interface CategoryRepository
{
     /**
     * This method will fetch a single Category from the dsatabase utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Category
     * @throws NotFoundException
     */
    public function findById(int $id): Category;

    public function fetchAll(Paging $paging, CategorySearchFields $category_search_fields);

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
    ): int;
    public function slugExists(string $slug): bool;
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
    ): int;
}
