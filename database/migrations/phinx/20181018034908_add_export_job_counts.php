<?php

use Phinx\Migration\AbstractMigration;

class AddExportJobCounts extends AbstractMigration
{
    public function change()
    {
        $this->table('export_job')
            ->addColumn('total_rows', 'integer', [
                'null' => true,
            ])
            ->addColumn('total_batches', 'integer', [
                'null' => true,
            ])
            ->update();
    }
}
