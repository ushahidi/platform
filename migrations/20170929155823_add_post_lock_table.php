<?php
use Phinx\Migration\AbstractMigration;

class AddPostLockTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('post_locks')
          ->addColumn('post_id', 'integer')
          ->addColumn('user_id', 'integer')
          ->addColumn('expires', 'integer', ['null' => false])
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
        $this->dropTable('post_locks');
    }
}
