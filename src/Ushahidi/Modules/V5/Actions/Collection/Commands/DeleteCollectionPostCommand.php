<?php

namespace Ushahidi\Modules\V5\Actions\Collection\Commands;

use App\Bus\Command\Command;

class DeleteCollectionPostCommand implements Command
{


    /**
     * @var int
     */
    private $post_id;

    
    /**
     * @var int
     */
    private $collection_id;

    public function __construct(int $collection_id, int $post_id)
    {
        $this->post_id = $post_id;
        $this->collection_id = $collection_id;
    }


    /**
     * @return int
     */
    public function getPostId(): int
    {
        return $this->post_id;
    }

    /**
     * @return int
     */
    public function getCollectionId(): int
    {
        return $this->collection_id;
    }
}
