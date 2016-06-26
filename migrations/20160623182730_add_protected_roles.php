<?php

use Phinx\Migration\AbstractMigration;

class AddProtectedRoles extends AbstractMigration
{
    
    /**
     * Migrate Up.
     */
    public function up()
    {
		$this->table('roles')
            ->addColumn('protected', 'boolean', [
                'after' => 'id',
                'null' => false,
				'default' => false
            ])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
		$this->table('roles')
			 ->removeColumn('protected')
            ->update();
    }
}
