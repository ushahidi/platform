<?php

use Phinx\Migration\AbstractMigration;

class AddFormAllRoles extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
		$this->table('forms')
            ->addColumn('all_roles', 'boolean', [
                'after' => 'require_approval',
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
			 ->removeColumn('all_roles')
             ->update();
    }
}