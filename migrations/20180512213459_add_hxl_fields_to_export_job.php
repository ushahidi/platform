<?php

use Phinx\Migration\AbstractMigration;

class AddHxlFieldsToExportJob extends AbstractMigration
{
    public function up()
    {
        $this->table('export_job')
            ->addColumn('include_hxl', 'boolean', [
                'default' => false,
                'comment' => 'Does this job include a row of hxl tags? ',
            ])
            ->addColumn('send_to_browser', 'boolean', [
                'default' => false,
                'comment' => 'Will we send the export to the user in the browser?',
            ])
            ->addColumn('send_to_hdx', 'boolean', [
                'default' => false,
                'comment' => 'Will we send the file to HDX?',
            ])
            ->addColumn(
                'hxl_heading_row',
                'text',
                [
                    'null' => true,
                    'limit' => Phinx\Db\Adapter\MysqlAdapter::TEXT_MEDIUM,
                    'default' => null,
                    'comment' => 'The computed row of HXL tags+attributes'
                ]
            )
            ->update();
    }

    public function down()
    {
        $this->table('export_job')
            ->removeColumn('include_hxl')
            ->removeColumn('send_to_browser')
            ->removeColumn('send_to_hdx')
            ->removeColumn('hxl_heading_row')
            ->update();
    }
}
