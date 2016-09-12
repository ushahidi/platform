<?php

use Phinx\Migration\AbstractMigration;

class AddRequireApprovalToForms extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('forms')
            ->addColumn('require_approval', 'boolean', [
                'after' => 'disabled',
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
        $this->table('forms')
             ->removeColumn('require_approval')
            ->update();
    }
}
