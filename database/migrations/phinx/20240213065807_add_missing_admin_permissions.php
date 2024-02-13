<?php


use Phinx\Migration\AbstractMigration;

class AddMissingAdminPermissions extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->execute("INSERT INTO `roles_permissions` (`role`, `permission`)
            VALUES ('admin', 'Manage Users')");

        $this->execute("INSERT INTO `roles_permissions` (`role`, `permission`)
            VALUES ('admin', 'Manage Posts')");

        $this->execute("INSERT INTO `roles_permissions` (`role`, `permission`)
            VALUES ('admin', 'Manage Settings')");

        $this->execute("INSERT INTO `roles_permissions` (`role`, `permission`)
            VALUES ('admin', 'Bulk Data Import and Export')");

        $this->execute("INSERT INTO `roles_permissions` (`role`, `permission`)
            VALUES ('admin', 'Edit their own posts')");

        $this->execute("INSERT INTO `roles_permissions` (`role`, `permission`)
            VALUES ('admin', 'Delete Posts')");

        $this->execute("INSERT INTO `roles_permissions` (`role`, `permission`)
            VALUES ('admin', 'Delete Their Own Posts')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->execute("DELETE FROM roles_permissions 
            WHERE permission = 'Manage Users'");

        $this->execute("DELETE FROM roles_permissions 
            WHERE permission = 'Manage Posts'");
        
        $this->execute("DELETE FROM roles_permissions 
            WHERE permission = 'Manage Settings'");
        
        $this->execute("DELETE FROM roles_permissions 
            WHERE permission = 'Bulk Data Import and Export'");

        $this->execute("DELETE FROM roles_permissions 
            WHERE permission = 'Edit their own posts'");
        
        $this->execute("DELETE FROM roles_permissions 
            WHERE permission = 'Delete Posts'");
        
        $this->execute("DELETE FROM roles_permissions 
            WHERE permission = 'Delete Their Own Posts'");
    }
}
