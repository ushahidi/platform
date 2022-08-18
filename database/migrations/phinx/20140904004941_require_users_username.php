<?php

use Phinx\Migration\AbstractMigration;

class RequireUsersUsername extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('users')
            ->changeColumn('username', 'string', [
                'limit' => 50,
                'null' => false,
                ])
            ->addIndex(['username'], ['unique' => true])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('users')
            ->changeColumn('username', 'string', [
                'limit' => 50,
                'null' => true,
                ])
            ->removeIndex(['username'], ['unique' => true])
            ->update();
    }
}
