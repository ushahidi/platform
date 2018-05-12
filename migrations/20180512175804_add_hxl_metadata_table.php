<?php

use Phinx\Migration\AbstractMigration;

class AddHxlMetadataTable extends AbstractMigration
{
    public function up()
    {
        $this->table('hxl_metadata') //TODO check with team on table name
            ->addColumn('private', 'boolean', [
                'default' => true,
                'comment' => 'Is this a private dataset in HDX? ',
            ])
            ->addColumn('dataset_title', 'text', [
                'null' => false,
                'limit' => 255,
                'comment' => 'Dataset title in HDX',
            ])
            ->addColumn('license_id', 'integer', [
                'null' => false,
                'comment' => 'Dataset license in HDX',
            ])
            ->addColumn('export_job_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('organisation', 'text', [
                'null' => false,
                'limit' => 255
            ])

            ->addColumn('source', 'text', [
                'null' => false,
                'limit' => 255
            ])
            ->addColumn('maintainer', 'text', [
                'null' => false,
                'limit' => 255
            ])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['null' => true])
            ->addForeignKey('user_id', 'users', 'id')
            ->addForeignKey('export_job_id', 'export_job', 'id')
            ->addForeignKey('license_id', 'hxl_license', 'id')
            ->create();
    }

    public function down()
    {
        $this->dropTable('hxl_metadata');
    }
}