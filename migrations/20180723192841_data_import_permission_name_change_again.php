<?php

use Phinx\Migration\AbstractMigration;

class DataImportPermissionNameChangeAgain extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE  permissions SET
                            name = 'Bulk Data Import and Export',
                            description = 'Import and export data in bulk'
                            WHERE name = 'Bulk Data Import';");
        $this->execute("UPDATE  roles_permissions SET
                            `permission` = 'Bulk Data Import and Export'
                            WHERE `permission` = 'Bulk Data Import';");
        $this->execute("UPDATE  permissions SET
                            name = 'Bulk Data Import and Export',
                            description = 'Import and export data in bulk'
                            WHERE name = 'Bulk Data Import';");
        $this->execute("UPDATE  roles_permissions SET
                            `permission` = 'Bulk Data Import and Export'
                            WHERE `permission` = 'Bulk Data Import';");
    }
    public function down()
    {
        $this->execute("UPDATE  permissions SET
                            name = 'Bulk Data Import',
                            description = 'Import data from external sources'
                            WHERE name = 'Bulk Data Import and Export';");
        $this->execute("UPDATE  roles_permissions SET
                            permission = 'Bulk Data Import'
                            WHERE permission = 'Bulk Data Import and Export';");
    }
}
