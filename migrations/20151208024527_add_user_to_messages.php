<?php

use Phinx\Migration\AbstractMigration;

class AddUserToMessages extends AbstractMigration
{

	/**
	 * Migrate Up.
	 */
	public function up()
	{
		$this->table('messages')
			->addColumn('user_id', 'integer', [
				'after' => 'post_id',
				'null' => true,
				'default' => null
			])
			->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'SET NULL',
                'update' => 'CASCADE',
                ])
			->update();

	}

	/**
	 * Migrate Down.
	 */
	public function down()
	{
		$this->table('messages')
			->dropForeignKey('user_id')
			->removeColumn('user_id')
			->update();
	}
}
