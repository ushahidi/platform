<?php

use Phinx\Migration\AbstractMigration;

class AllowNullExportBatchFilename extends AbstractMigration
{
    public function up()
    {
        $this->table('export_batches')
            ->changeColumn('filename', 'string', [
                'default' => '',
                'null' => true
            ])
            ->update();
    }

    public function down()
    {
        // No op. Don't reverse this or it causes bugs
    }
}
