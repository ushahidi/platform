<?php

use Phinx\Migration\AbstractMigration;

class AddExportJobBatches extends AbstractMigration
{
    public function change()
    {
        $this->table('export_batches') // @todo revisit name?
            ->addColumn('export_job_id', 'integer', ['null' => false])
            ->addColumn('status', 'string', [
                'null' => false,
                'default' => 'pending'
            ])
            ->addColumn('batch_number', 'integer', ['default' => 0])
            ->addColumn('filename', 'string')
            ->addColumn('has_headers', 'boolean', ['default' => false])
            ->addColumn('rows', 'integer', ['default' => 0])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['null' => true, 'default' => 0])
            ->addForeignKey(
                'export_job_id',
                'export_job',
                'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE']
            )
            ->create();
    }
}
