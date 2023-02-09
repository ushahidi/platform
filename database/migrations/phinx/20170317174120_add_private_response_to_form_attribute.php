<?php

use Phinx\Migration\AbstractMigration;

class AddPrivateResponseToFormAttribute extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('form_attributes')
          ->addColumn('response_private', 'boolean', [
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
        $this->table('form_attributes')
          ->removeColumn('response_private')
          ->update();
    }
}
