<?php

use Phinx\Migration\AbstractMigration;

class RenameSourceDestinationUuidForWebhooksTable extends AbstractMigration
{
    public function up()
    {
        $attributes = $this->table('webhooks');
        $attributes
            ->renameColumn('source_field_uuid', 'source_field_key')
            ->renameColumn('destination_field_uuid', 'destination_field_key')
            ->update();
    }

    public function down()
    {
        $attributes = $this->table('webhooks');
        $attributes
            ->renameColumn('source_field_key', 'source_field_uuid')
            ->renameColumn('destination_field_key', 'destination_field_uuid')
            ->update();
    }
}
