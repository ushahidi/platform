<?php

use Phinx\Migration\AbstractMigration;

class AddRolesPermissions extends AbstractMigration
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
		$this->table('roles_permissions')
			->addColumn('role_id', 'integer', ['null' => false])
			->addColumn('permission_name', 'string', ['limit' => 50, 'null' => false])
			->addForeignKey('role_id', 'roles', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                ])
			->addForeignKey('permission_name', 'permissions', 'name', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                ])
			->create()
			;
    }
}
