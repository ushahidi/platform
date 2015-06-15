<?php

use Phinx\Migration\AbstractMigration;

class AddDefaultRoles extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {

        $admin = $this->fetchRow("SELECT COUNT(*) FROM roles WHERE name = 'admin'", 0);
        if (! $admin[0]) {
            $this->execute("INSERT INTO roles (name, display_name, description)
                VALUES
                    ( 'admin', 'Admin', 'Administrator' )
                ");
        }

        $user = $this->fetchRow("SELECT COUNT(*) FROM roles WHERE name = 'user'", 0);
        if (! $user[0]) {
            $this->execute("INSERT INTO roles (name, display_name, description)
                VALUES
                    ( 'user', 'User', 'Registered member' )
                ");
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // noop, these should have been in from day 0
    }
}
