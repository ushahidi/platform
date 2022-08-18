<?php

use Phinx\Migration\AbstractMigration;

class AddHideAuthorToForm extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('forms')
          ->addColumn('hide_author', 'boolean', [
              'default' => false,
              'null' => false,
              ])
          ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('forms')
          ->removeColumn('hide_author')
          ->update();
    }
}
