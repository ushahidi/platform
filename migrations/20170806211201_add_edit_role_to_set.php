<?php

use Phinx\Migration\AbstractMigration;

class AddEditRoleToSet extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('sets')
          ->addColumn('edit_role', 'string', [
				'limit' => 150,
				'null'  => true,
                'default' => '[]'
			])
          ->update();
    }
    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('sets')
            ->removeColumn('edit_role')
            ->update();
    }
}
