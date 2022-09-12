<?php

use Phinx\Migration\AbstractMigration;

class CreateImportMappingTable extends AbstractMigration
{
    public function change()
    {
        $this->table('import_mappings')
            ->addColumn('import_id', 'integer')
            ->addColumn('source_type', 'string')
            ->addColumn('source_id', 'string')
            ->addColumn('dest_type', 'string')
            ->addColumn('dest_id', 'integer')
            ->addColumn('established_by', 'string')
            ->addForeignKey(
                'import_id',
                'imports',
                'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE']
            )
            ->addIndex(['import_id', 'source_type', 'source_id'])
            ->create();
    }
}
