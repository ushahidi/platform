<?php

use Phinx\Migration\AbstractMigration;

class RenameUserToMember extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("UPDATE roles SET display_name = 'Member' WHERE name = 'user' AND display_name = 'User'");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("UPDATE roles SET display_name = 'User' WHERE name = 'user' AND display_name = 'Member'");
    }
}
