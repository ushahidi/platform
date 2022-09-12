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
            ->addColumn('role', 'string', ['limit' => 50, 'null' => false])
            ->addColumn('permission', 'string', ['limit' => 50, 'null' => false])
            ->addForeignKey('role', 'roles', 'name', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                ])
            ->addForeignKey('permission', 'permissions', 'name', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                ])
            ->create()
            ;
    }
}
