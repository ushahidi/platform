<?php

use Phinx\Migration\AbstractMigration;

class AddTypeToStage extends AbstractMigration
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
          ->addColumn('type', 'string', [
            'default' => 'task',
            ])
          ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('form_stages')
          ->removeColumn('type')
          ->update();
    }
}
