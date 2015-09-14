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
            ->addColumn('can_notify', 'integer', [
                'after' => 'updated',
                'null' => false,
				'default' => '0'
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
