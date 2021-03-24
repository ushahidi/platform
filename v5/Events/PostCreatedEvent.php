<?php
namespace v5\Events;

use Illuminate\Queue\SerializesModels;
use v5\Models\Post\Post;

class PostCreatedEvent
{
    use SerializesModels;

    public $post;

    /**
     * Create a new event instance.
     *
     * @param  Post  $post
     * @return void
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}
