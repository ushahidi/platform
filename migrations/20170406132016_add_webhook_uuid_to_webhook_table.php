<?php

use Phinx\Migration\AbstractMigration;

class AddWebhookUuidToWebhookTable extends AbstractMigration
{
   /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('webhooks')
          ->addColumn('webhook_uuid', 'string', ['null' => false])
          ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('webhooks')
          ->removeColumn('webhook_uuid')
          ->update();
    }
}
