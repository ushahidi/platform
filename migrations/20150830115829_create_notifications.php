<?php

use Phinx\Migration\AbstractMigration;

class CreateNotifications extends AbstractMigration
{
	/**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     **/
    public function change()
    {
        $this->table('notifications')
            ->addColumn('contact_id', 'integer', ['null' => false])
            ->addColumn('set_id', 'integer', ['null' => false])
			->addColumn('is_subscribed', 'integer', ['default' => 0])
            ->addColumn('created', 'integer', ['default' => 0])
			->addColumn('updated', 'integer', ['default' => 0])
            ->addForeignKey('contact_id', 'contacts', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                ])
            ->create();
    }
}