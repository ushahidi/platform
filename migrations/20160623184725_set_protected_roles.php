<?php

use Phinx\Migration\AbstractMigration;

class SetProtectedRoles extends AbstractMigration
{
    
    /**
     * Migrate Up.
     */
    public function up()
    {
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
