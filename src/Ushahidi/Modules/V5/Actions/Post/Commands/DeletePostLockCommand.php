<?php

namespace Ushahidi\Modules\V5\Actions\Post\Commands;

use App\Bus\Command\Command;

class DeletePostLockCommand implements Command
{
    /**
     * @var int
     */
    private $post_id;

    public function __construct(int $post_id)
    {
        $this->post_id = $post_id;
    }

    public function getPostId(): int
    {
        return $this->post_id;
    }
}
