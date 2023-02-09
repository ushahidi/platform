<?php

use Phinx\Migration\AbstractMigration;

class AddMetaDataKeyToExportJob extends AbstractMigration
{
    public function change()
    {
        $this->table('export_job')
            ->addColumn('hxl_meta_data_id', 'integer', [
                'null' => true,
            ])
            ->addIndex(['hxl_meta_data_id'], ['unique' => true,'name' => 'hxl_meta_data_id_unique'])
            ->addForeignKey(
                'hxl_meta_data_id',
                'hxl_meta_data',
                'id',
                ['delete'=>'CASCADE', 'update'=>'CASCADE']
            )
            ->update();
    }
}
