<?php

use Phinx\Migration\AbstractMigration;

class AddHeaderToExportJob extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('export_job')
            ->addColumn(
                'header_row',
                'text',
				['null' => true, 'limit' => 16777215, 'default' => null]
			)
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('export_job')
            ->removeColumn('header_row')
            ->update();
    }
}
