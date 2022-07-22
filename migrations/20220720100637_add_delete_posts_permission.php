<?php

use Phinx\Migration\AbstractMigration;

class AddDeletePostsPermission extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->execute("INSERT INTO permissions (name, description)
            VALUES ('Delete Posts', 'Delete Posts')");

        $this->execute("INSERT INTO roles_permissions(`role`,`permission`) 
            SELECT `role`,'Delete Posts' FROM roles_permissions WHERE `permission` = 'Manage Users'
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        $this->execute("DELETE FROM permissions WHERE name = 'Delete Posts'");

        $this->execute("DELETE FROM roles_permissions WHERE permission = 'Delete Posts'");
    }
}
