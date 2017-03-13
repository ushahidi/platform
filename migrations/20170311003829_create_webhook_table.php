<?php

use Phinx\Migration\AbstractMigration;

class CreateWebhookTable extends AbstractMigration
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
      $this->table('webhooks')
          ->addColumn('user_id', 'integer', ['null' => false])
          ->addColumn('url', 'string', ['null' => false])
          ->addColumn('shared_secret', 'string', ['null' => false])
          ->addColumn('event_type', 'string', ['null' => false])
          ->addColumn('entity_type', 'string', ['null' => false])
          ->addColumn('created', 'integer', ['default' => 0])
          ->addColumn('updated', 'integer', ['default' => 0], ['null' => true])
          ->create();

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
      $this->dropTable('webhooks');
    }
}
