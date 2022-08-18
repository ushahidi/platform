<?php

use Phinx\Migration\AbstractMigration;

class AddHxlMetadataTable extends AbstractMigration
{
    public function up()
    {
        $this->table('hxl_meta_data')
            ->addColumn('private', 'boolean', [
                'default' => true,
                'comment' => 'Is this a private dataset in HDX? ',
            ])
            ->addColumn('dataset_title', 'string', [
                'null' => false,
                'limit' => 255,
                'comment' => 'Dataset title in HDX',
            ])
            ->addColumn('license_id', 'integer', [
                'null' => false,
                'comment' => 'Dataset license in HDX',
            ])
            ->addColumn('user_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('organisation', 'string', [
                'null' => false,
                'limit' => 255
            ])
            ->addColumn('source', 'string', [
                'null' => false,
                'limit' => 255
            ])
            ->addColumn('maintainer', 'string', [
                'null' => false,
                'limit' => 255
            ])
            ->addIndex('dataset_title')
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['null' => true])
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                ['delete'=>'CASCADE', 'update'=>'CASCADE']
            )
            ->addForeignKey(
                'license_id',
                'hxl_license',
                'id',
                ['delete'=>'CASCADE', 'update'=>'CASCADE']
            )
            ->create();
    }

    public function down()
    {
        $this->dropTable('hxl_meta_data');
    }
}
