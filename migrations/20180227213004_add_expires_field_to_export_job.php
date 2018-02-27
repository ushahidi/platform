<?php

use Phinx\Migration\AbstractMigration;

class AddExpiresFieldToExportJob extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('export_job')
          ->addColumn('url_expiration', 'string', [
              'null' => true,
              'default' => false,
              'limit' => 12
              ])
          ->addColumn('status_details', 'string', [
                'null' => true,
                'default' => false,
            ])
          ->changeColumn('url', 'text', ['null' => true])
          ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('export_job')
          ->removeColumn('url_expiration')
          ->removeColumn('status_details')
          ->changeColumn('url', 'string', ['null' => true])
          ->update();
    }
}
