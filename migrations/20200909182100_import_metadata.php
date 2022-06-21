<?php

use Phinx\Migration\AbstractMigration;

class ImportMetadata extends AbstractMigration
{
    public function change()
    {
        $this->table('imports')
            ->addColumn('metadata', 'json', [
                'null' => true,
            ])
            ->update();
    }
}
