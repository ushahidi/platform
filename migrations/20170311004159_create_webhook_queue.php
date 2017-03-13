<?php

use Phinx\Migration\AbstractMigration;

class CreateWebhookQueue extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */

    /**
     * Migrate Up.
     */
    public function up()
    {
      $this->table('webhook_queue')
        ->addColumn('user_id', 'integer', ['null' => false])
		    ->addColumn('post_id', 'integer', ['null' => false])
        ->addColumn('webhook_id', 'integer', ['null' => false])
  		  ->addColumn('created', 'integer', ['default' => 0])
  		  ->addForeignKey('post_id', 'posts', 'id', [
  				'delete' => 'CASCADE',
  				'update' => 'CASCADE',
  		  ])
  			->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
      $this->dropTable('webhook_queue');
    }
}
