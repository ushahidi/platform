<?php

use Phinx\Migration\AbstractMigration;

class AddManageCollectionsPermission extends AbstractMigration
{
    public function up()
    {
        $this->execute("INSERT INTO permissions (name, description)
            VALUES ('Manage Collections and Saved Searches', 'Manage collections and saved searches')
        ");

        $this->execute("INSERT INTO `roles_permissions` (`role`, `permission`)
            VALUES ('admin', 'Manage Collections and Saved Searches')
        ");
    }

    public function down()
    {
        $this->execute("DELETE FROM permissions WHERE name = 'Manage Collections and Saved Searches'");

        $this->execute("DELETE FROM roles_permissions WHERE permission = 'Manage Collections and Saved Searches'");
    }
}
