<?php

use Phinx\Migration\AbstractMigration;

class AddFormSourceDestinationToWebhookTable extends AbstractMigration
{
   /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('webhooks')
          ->addColumn('form_id', 'integer', [
              'default' => null,
              'null' => true
          ])
          ->addColumn('source_field_uuid', 'string', ['null' => true])
          ->addColumn('destination_field_uuid', 'string', ['null' => true])
          ->addForeignKey('form_id', 'forms', 'id', [
              'delete'=> 'CASCADE'
          ])
          ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('webhooks')
          ->dropForeignKey('form_id')
          ->removeColumn('form_id')
          ->removeColumn('source_field_uuid')
          ->removeColumn('destination_field_uuid')
          ->update();
    }
}
