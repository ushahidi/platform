<?php

use Phinx\Migration\AbstractMigration;

class CreateImportTable extends AbstractMigration
{
    public function change()
    {
        $this->table('imports')
            ->addColumn('status', 'string', [
                'null' => false,
                'default' => 'pending'
            ])
            ->addColumn('type', 'string', ['default' => 'ushahidiv2'])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['null' => true, 'default' => 0])
            ->create();
    }
}
