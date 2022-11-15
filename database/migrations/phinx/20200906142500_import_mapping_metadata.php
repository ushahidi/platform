<?php

use Phinx\Migration\AbstractMigration;

class ImportMappingMetadata extends AbstractMigration
{
    public function change()
    {
        $this->table('import_mappings')
            ->addColumn('metadata', 'text', [
                'null' => true,
            ])
            ->update();
    }
}
