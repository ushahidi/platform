<?php

use Phinx\Migration\AbstractMigration;

class RenameDataProviderToSource extends AbstractMigration
{
    public function change()
    {
        $this->table('messages')
            ->renameColumn('data_provider', 'data_source')
            ->renameColumn('data_provider_message_id', 'data_source_message_id')
            ->update();

        $this->table('contacts')
            ->renameColumn('data_provider', 'data_source')
            ->update();
    }
}
