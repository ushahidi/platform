<?php

use Phinx\Migration\AbstractMigration;

class AddPostsChangelogTable extends AbstractMigration
{

     /**
      * Migrate Up.
      */
     public function up()
     {
         $this->table('posts_changelog')
         //string = Text
         ->addColumn('created', 'string', ['limit' => 11])
         ->addColumn('user_id', 'integer', ['null' => false])
         ->addColumn('post_id', 'integer', ['null' => false])
         ->addColumn('change_type', 'string', ['null' => true, 'limit' => 50])
         ->addColumn('item_changed', 'string', ['null' => true, 'limit' => 50])
         ->addColumn('content', 'string', ['null' => true])
         ->addColumn('entry_type', 'string', ['null' => true, 'limit' => 1])
         ->addForeignKey('user_id', 'users', 'id', [
                     'delete' => 'CASCADE',
                     'update' => 'CASCADE',
                     ])
         ->addForeignKey('post_id', 'posts', 'id', [
                     'delete' => 'CASCADE',
                     'update' => 'CASCADE',
                     ])
           ->create();
     }

     /**
      * Migrate Down.
      */
     public function down()
     {
         $this->dropTable('posts_changelog');
     }
}
