<?php

use Phinx\Migration\AbstractMigration;

class AddContactNotifyFlag extends AbstractMigration
{
    
    /**
     * Migrate Up.
     */
    public function up()
    {
		$this->table('contacts')
            ->addColumn('can_notify', 'boolean', [
                'after' => 'updated',
                'null' => false,
				'default' => false
            ])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
		$this->table('contacts')
			 ->removeColumn('can_notify')
            ->update();
    }
}
