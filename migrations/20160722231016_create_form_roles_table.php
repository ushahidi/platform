<?php

use Phinx\Migration\AbstractMigration;

class CreateFormRolesTable extends AbstractMigration
{
    public function change()
    {
        $this->table('form_roles')
            ->addColumn('form_id', 'integer', ['null' => false])
            ->addColumn('role_id', 'integer', ['null' => false])
            ->addForeignKey('form_id', 'forms', 'id', array('delete'=> 'CASCADE', 'update'=> 'NO_ACTION'))
            ->addForeignKey('role_id', 'roles', 'id', array('delete'=> 'CASCADE', 'update'=> 'NO_ACTION'))
            ->create()
            ;
    }
}