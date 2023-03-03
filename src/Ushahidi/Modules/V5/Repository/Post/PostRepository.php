<?php

namespace Ushahidi\Modules\V5\Repository\Post;

use Ushahidi\Modules\V5\Entity\PostEntity;

interface PostRepository
{
    public function fetchById(int $id): ?PostEntity;
}
