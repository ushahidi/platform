<?php

use Phinx\Migration\AbstractMigration;

class AddEditOwnPostsPermission extends AbstractMigration
{
    public function up()
    {
        $this->execute("INSERT INTO permissions (name, description)
            VALUES ('Edit their own posts', 'Edit their own posts')
            ");

        $this->execute("INSERT INTO `roles_permissions` (`role`, `permission`)
            VALUES ('user', 'Edit their own posts')
            ");
    }

    public function down()
    {
        $this->execute("DELETE FROM permissions WHERE name = 'Edit their own posts'");

        $this->execute("DELETE FROM roles_permissions WHERE permission = 'Edit their own posts'");
    }
}
