<?php

namespace Ushahidi\Modules\V5\Repository\Post;

use Ushahidi\Modules\V5\Models\Post\Post;

interface PostRepository
{
    public function fetchById(int $id): Post;
}
