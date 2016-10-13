<?php

use Phinx\Migration\AbstractMigration;

class PostCustomDate extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('posts')
          ->addColumn('post_date', 'datetime', [
              'null' => true,
              ])
          ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('posts')
          ->removeColumn('post_date')
          ->update();
    }
}
