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
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('set_id', 'integer', ['null' => false])
            ->addColumn('created', 'integer', ['default' => 0])
			->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                ])
			->addForeignKey('set_id', 'sets', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                ])
            ->create()
			;
    }
}
