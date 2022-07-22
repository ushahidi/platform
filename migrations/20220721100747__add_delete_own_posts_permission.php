<?php

use Phinx\Migration\AbstractMigration;


class AddDeleteOwnPostsPermission extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->execute("INSERT INTO permissions (name, description)
            VALUES ('Delete Their Own Posts', 'Delete Their Own Posts')");
        
        $this->execute("INSERT INTO roles_permissions(`role`,`permission`) 
            SELECT `role`,'Delete Their Own Posts' FROM roles_permissions WHERE `permission` = 'Edit their own posts'
         ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->execute("DELETE FROM permissions WHERE name = 'Delete Their Own Posts'");
       
        $this->execute("DELETE FROM roles_permissions WHERE permission = 'Delete Their Own Posts'");

    }
}
