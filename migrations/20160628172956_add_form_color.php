<?php

use Phinx\Migration\AbstractMigration;

class AddFormColor extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('forms')
          ->addColumn('color', 'string', [
              'limit' => 6,
              'null' => true,
              ])
          ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('forms')
          ->removeColumn('color')
          ->update();
    }
}
