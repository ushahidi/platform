<?php

namespace Ushahidi\Modules\V5\Repository\Post;

use Illuminate\Database\Connection;
use Ushahidi\Modules\V5\Entity\PostEntity;

class EloquentPostRepository implements PostRepository
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function fetchById(int $id): ?PostEntity
    {
        $post = $this->db->table('posts')->find($id);

        if ($post === null) {
            return null;
        }

        return PostEntity::fromModel($post);
    }
}
