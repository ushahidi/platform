<?php

namespace Ushahidi\Modules\V5\Entity;

use Ushahidi\Modules\V5\Models\Post\Post;

class PostEntity
{
    private $id;

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromModel(Post $post): self
    {
        return new self($post->id);
    }

    public function getId(): int
    {
        return $this->id;
    }
}
