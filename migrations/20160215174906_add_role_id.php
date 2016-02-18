<?php

use Phinx\Migration\AbstractMigration;

class AddRoleId extends AbstractMigration
{
    /**
     * Migrate Up.
     *
     * @todo Change ALTER TABLE syntax to use Phinx API
     */
    public function up()
    {
        $this->table('roles')
            ->addColumn('id', 'integer')
            ->update();
        $this->table('roles')
            ->addIndex('name', ['unique' => true])
            ->update();
        $this->execute("ALTER TABLE roles DROP PRIMARY KEY;");
        $this->execute("ALTER TABLE roles MODIFY id INT AUTO_INCREMENT PRIMARY KEY;");

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("ALTER TABLE roles DROP PRIMARY KEY;");
        $this->execute("ALTER TABLE roles ADD PRIMARY KEY(name);");
        $this->execute("ALTER TABLE roles DROP INDEX name;");
        $this->table('roles')
            ->removeColumn('user_id')
            ->update();
    }
}
