<?php
use Phinx\Migration\AbstractMigration;

class AddPostCommentsTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('post_comments')
          ->addColumn('post_id', 'integer')
          ->addColumn('user_id', 'integer')
          ->addColumn('comment', 'text')
          ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
          ])
          ->addForeignKey('post_id', 'posts', 'id', [
              'delete' => 'CASCADE',
              'update' => 'CASCADE',
          ])
          ->create()
          ;
    }
    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('post_comments');
    }
}
