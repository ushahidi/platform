<?php

use Phinx\Migration\AbstractMigration;

class CreateWebhookTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('webhooks')
          ->addColumn('user_id', 'integer', ['null' => false])
          ->addColumn('name', 'string', ['null' => false])
          ->addColumn('url', 'string', ['null' => false])
          ->addColumn('shared_secret', 'string', ['null' => false])
          ->addColumn('event_type', 'string', ['null' => false])
          ->addColumn('entity_type', 'string', ['null' => false])
          ->addColumn('created', 'integer', ['default' => 0])
          ->addColumn('updated', 'integer', ['default' => 0], ['null' => true])
          ->addForeignKey('user_id', 'users', 'id', [
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
        $this->dropTable('webhooks');
    }
}
