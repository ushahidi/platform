<?php

use Phinx\Migration\AbstractMigration;

class AddPermissions extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     **/
    public function change()
    {
		$this->table('permissions')
			->addColumn('name', 'string', ['limit' => 50, 'null' => false])
			->addColumn('description', 'string', ['null' => true])
			->addIndex(['name'], ['unique' => true])
			->create()
			;
    }
}
