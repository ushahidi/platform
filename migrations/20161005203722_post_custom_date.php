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

        // Set default values for post_date
        $this->execute("UPDATE posts SET post_date = FROM_UNIXTIME(created);");
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
