<?php

use Phinx\Migration\AbstractMigration;

class RenameVisibleToColumn extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('sets')
        ->renameColumn('visible_to', 'role')
        ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('sets')
        ->renameColumn('role', 'visible_to')
        ->update();
    }
}
