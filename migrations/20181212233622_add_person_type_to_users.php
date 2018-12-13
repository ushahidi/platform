<?php

use Phinx\Migration\AbstractMigration;

class AddPersonTypeToUsers extends AbstractMigration
{
    /**
     * Add person_type column to users
     */
    public function change()
    {
        $this->table('users')
            ->addColumn('person_type', 'enum', [
                'values' => ['user', 'contact'],
                'default' => 'user',
                'after' => 'role',
                'comment' => 'Is this a full user who can log in or a contact?'
            ])
            ->update();
    }
}
