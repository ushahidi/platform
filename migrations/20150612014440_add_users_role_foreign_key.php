<?php

use Phinx\Migration\AbstractMigration;

class AddUsersRoleForeignKey extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('UPDATE users SET role = "user" WHERE role NOT IN (SELECT name from roles)');

        $this->table('users')
            ->changeColumn('role', 'string', [
                'limit' => 50,
                'null' => true,
                'default' => 'user'
                ])
            ->update();

        // Add foreign keys to users table
        $this->table('users')
            ->addForeignKey('role', 'roles', 'name', [
                'delete' => 'SET NULL',
                'update' => 'CASCADE',
                ])
            ->update()
            ;

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Remove foreign keys from users table
        $this->table('users')
            ->dropForeignKey('role')
            ->changeColumn('role', 'string', [
                'limit' => 50,
                'null' => false,
                ])
            ->update()
            ;
    }
}
