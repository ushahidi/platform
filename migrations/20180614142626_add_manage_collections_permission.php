<?php

use Phinx\Migration\AbstractMigration;

class AddManageCollectionsPermission extends AbstractMigration
{
    public function up()
    {
        $this->execute("INSERT INTO permissions (name, description)
            VALUES ('Manage Collections', 'Manage collections')
        ");

        $this->execute("INSERT INTO `roles_permissions` (`role`, `permission`)
            VALUES ('admin', 'Manage Collections')
        ");
    }

    public function down()
    {
        $this->execute("DELETE FROM permissions WHERE name = 'Manage Collections'");

        $this->execute("DELETE FROM roles_permissions WHERE permission = 'Manage Collections'");
    }
}


