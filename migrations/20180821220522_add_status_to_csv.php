<?php

use Phinx\Migration\AbstractMigration;

class AddStatusToCsv extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('csv')
          ->addColumn('status', 'string', ['null' => true])
          ->addColumn('errors', 'string', ['null' => true])
          ->addColumn('processed', 'string', ['null' => true])
          ->addColumn('collection_id', 'integer', ['null' => true])
          ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('csv')
          ->removeColumn('status')
          ->removeColumn('errors')
          ->removeColumn('processed')
          ->removeColumn('collection_id')
          ->update();
    }
}
