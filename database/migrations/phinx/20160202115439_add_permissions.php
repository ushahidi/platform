<?php

use Phinx\Migration\AbstractMigration;

class AddPermissions extends AbstractMigration
{
    public function up()
    {
        $this->table('permissions')
            ->addColumn('name', 'string', ['limit' => 50, 'null' => false])
            ->addColumn('description', 'string', ['null' => true])
            ->addIndex(['name'], ['unique' => true])
            ->create()
            ;

        $this->execute("INSERT INTO permissions (name, description)
                VALUES ('Manage Users', 'Manage users'),
                       ('Manage Posts', 'Manage posts'),
                       ('Manage Settings', 'Manage general settings'),
                       ('Bulk Data Import', 'Import data from external sources')
                ");
    }

    public function down()
    {
        $this->dropTable('permissions');
    }
}
