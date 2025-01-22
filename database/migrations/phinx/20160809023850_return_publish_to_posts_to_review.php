<?php

use Phinx\Migration\AbstractMigration;

class ReturnPublishToPostsToReview extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        // Convert posts with published_to = NULL or empty
        $this->execute("UPDATE posts SET published_to = '[]'
            WHERE (published_to IS NULL OR published_to = '')");
        // Convert posts with `published_to` including `['user']` to published posts
        $this->execute("UPDATE posts SET status = 'published', published_to = '[]'
            WHERE status = 'published' AND published_to LIKE '%user%'");
        // Convert posts with `published_to` doesn't include `['user']` to draft posts
        $this->execute("UPDATE posts SET status = 'draft', published_to = '[]'
            WHERE status = 'published' AND published_to <> '[]' AND published_to NOT LIKE '%user%'");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // No way to revert this change
    }
}
