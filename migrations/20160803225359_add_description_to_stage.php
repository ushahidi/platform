<?php

use Phinx\Migration\AbstractMigration;

class AddDescriptionToStage extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('form_stages')
          ->addColumn('description', 'string', [
            'null' => true,
            ])
          ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('form_stages')
          ->removeColumn('description')
          ->update();
    }
}
