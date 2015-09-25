<?php

use Phinx\Migration\AbstractMigration;

class CreateNotificationQueue extends AbstractMigration
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
		$this->table('notification_queue')
			 ->addColumn('post_id', 'integer', ['null' => false])
			 ->addColumn('set_id', 'integer', ['null' => false])
			 ->addColumn('created', 'integer', ['default' => 0])
			 ->addForeignKey('post_id', 'posts', 'id', [
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
