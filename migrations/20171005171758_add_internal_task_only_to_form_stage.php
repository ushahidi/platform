<?php

use Phinx\Migration\AbstractMigration;

class AddInternalTaskOnlyToFormStage extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('form_stages')
          ->addColumn('task_is_internal_only', 'boolean', [
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
          ->removeColumn('task_is_internal_only')
          ->update();
    }
}
