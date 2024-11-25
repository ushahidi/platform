<?php

namespace Ushahidi\Modules\V5\Repository\Category;

use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Category;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\CategorySearchFields;
use Ushahidi\Core\Tool\SearchData;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepository
{
     /**
     * This method will fetch a single Category from the dsatabase utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @param array $fields
     * @param array $with
     * @return Category
     * @throws NotFoundException
     */
    public function findById(int $id, array $fields = [], array $with = []): Category;


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
    ): LengthAwarePaginator;

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
    ): int;
    public function slugExists(string $slug): bool;
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
    ): int;

    public function setSearchParams(SearchData $searchData);
}
