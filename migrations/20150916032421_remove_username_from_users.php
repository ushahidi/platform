<?php

use Phinx\Migration\AbstractMigration;

class RemoveUsernameFromUsers extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('UPDATE users SET realname = username WHERE realname IS NULL OR realname = ""');
        $this->execute('UPDATE users SET email = username WHERE email IS NULL OR email = ""');

        $this->table('users')
            ->removeColumn('username')
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('users')
            ->addColumn('username', 'string', [
                'limit' => 50,
                'null' => false,
                ])
            ->update();
    }
}
