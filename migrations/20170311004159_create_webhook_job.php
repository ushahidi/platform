<?php

use Phinx\Migration\AbstractMigration;

class CreateWebhookJob extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('webhook_job')
  		    ->addColumn('post_id', 'integer', ['null' => false])
          ->addColumn('event_type', 'string', ['null' => false])
    		  ->addColumn('created', 'integer', ['default' => 0])
    		  ->addForeignKey('post_id', 'posts', 'id', [
    				'delete' => 'CASCADE'
    		  ])
    			->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('webhook_job');
    }
}
