<?php

use Phinx\Migration\AbstractMigration;

class RemovePermissionsFromRoles extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('roles');
        $table->removeColumn('permissions')
              ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('roles');
        $table->addColumn('permissions')
              ->update();
    }
}
