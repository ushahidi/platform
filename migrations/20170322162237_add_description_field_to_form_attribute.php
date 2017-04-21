<?php

use Phinx\Migration\AbstractMigration;

class AddDescriptionFieldToFormAttribute extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('form_attributes')
          ->addColumn('description', 'string', [
              'null' => true
              ])
          ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('form_attributes')
          ->removeColumn('description')
          ->update();
    }
}
