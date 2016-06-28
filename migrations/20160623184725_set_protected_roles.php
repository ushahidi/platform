<?php

use Phinx\Migration\AbstractMigration;

class SetProtectedRoles extends AbstractMigration
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
	    
        $this->execute("UPDATE roles
        	SET protected = 1
            WHERE
                name = 'user' OR name = 'admin'
            ");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
