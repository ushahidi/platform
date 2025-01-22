<?php

namespace Ushahidi\Modules\V5\Actions\Post\Queries;

use App\Bus\Query\Query;

class FetchPostLockByPostIdQuery implements Query
{
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
