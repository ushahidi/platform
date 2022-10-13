<?php

namespace Ushahidi\Modules\V5\Repository;

use Ushahidi\Core\Entity\Tag;
use Illuminate\Support\Collection;
use Ushahidi\Core\EloquentRepository;
use Ushahidi\Contracts\Repository\Entity\TagRepository as TagRepositoryInterface;

class TagRepository extends EloquentRepository implements TagRepositoryInterface
{
    protected static $root = Tag::class;

    public function doesTagExist($value)
    {
    }

    public function createMany(Collection $collection)
    {
        return [];
    }
}
