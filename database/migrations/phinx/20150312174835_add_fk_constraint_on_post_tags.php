<?php

use Phinx\Migration\AbstractMigration;

class AddFkConstraintOnPostTags extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("DELETE FROM posts_tags WHERE tag_id NOT IN (SELECT id from tags)");
        $this->execute("DELETE FROM posts_tags WHERE post_id NOT IN (SELECT id from posts)");

        // Add foreign keys to posts_tags table
        $this->table('posts_tags')
            ->addForeignKey('tag_id', 'tags', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                ])
            ->addForeignKey('post_id', 'posts', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                ])
            ->update()
            ;
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Add foreign keys to posts_tags table
        $this->table('posts_tags')
            ->dropForeignKey('tag_id')
            ->dropForeignKey('post_id')
            ->update()
            ;
    }
}
