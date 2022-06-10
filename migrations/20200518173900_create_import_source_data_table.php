<?php

use Phinx\Migration\AbstractMigration;

class CreateImportSourceDataTable extends AbstractMigration
{
    public function change()
    {
        $this->table('import_source_datas')
            ->addColumn('import_id', 'integer')
            ->addColumn('source_table', 'string')
            ->addColumn('row_id', 'string')
            ->addColumn('data', 'json')
            ->addForeignKey(
                'import_id',
                'imports',
                'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE']
            )
            ->addIndex(['import_id', 'source_table', 'row_id'])
            ->create();
    }
}
