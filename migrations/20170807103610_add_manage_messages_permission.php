<?php

use Phinx\Migration\AbstractMigration;

class AddManageMessagesPermission extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("INSERT INTO permissions (name, description) VALUES ('Manage Messages', 'Manage messages')");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("DELETE FROM permissions WHERE name = 'Manage Messages'");
    }
}
