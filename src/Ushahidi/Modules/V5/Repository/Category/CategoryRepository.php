<?php

namespace Ushahidi\Modules\V5\Repository\Category;

use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Category;

interface CategoryRepository
{
    /**
     * @throws \Exception if category is not found
     */
    public function fetchByIdOrFail(int $id): Category;

    public function fetchAll(): Collection;

    public function store(
        ?string $parentId,
        string $tag,
        string  $slug,
        string  $type,
        ?string $description,
        ?string $color,
        ?string  $icon,
        int     $priority,
        ?array  $role,
        string $defaultBaseLanguage,
        array $availableLanguages
    ): int;

    public function slugExists(string $slug): bool;

    public function update(
        int $id,
        ?string $parentId,
        ?string $tag,
        ?string  $slug,
        ?string  $type,
        ?string $description,
        ?string $color,
        ?string  $icon,
        ?int     $priority,
        ?array  $role,
        ?string $defaultBaseLanguage,
        ?array $availableLanguages
    ): int;
}
