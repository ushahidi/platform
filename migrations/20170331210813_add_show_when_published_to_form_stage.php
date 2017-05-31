<?php

use Phinx\Migration\AbstractMigration;

class AddShowWhenPublishedToFormStage extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('form_stages')
          ->addColumn('show_when_published', 'boolean', [
              'null' => false,
              'default' => true
              ])
          ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('form_stages')
          ->removeColumn('show_when_published')
          ->update();
    }
}
