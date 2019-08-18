<?php

use Phinx\Migration\AbstractMigration;

class AddApiKeyTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('apikeys')
          ->addColumn('api_key', 'text', ['null' => false])
          ->addColumn('client_id', 'text', ['null' => true])
          ->addColumn('client_secret', 'text', ['null' => true])
          ->addColumn('created', 'integer', ['default' => 0])
          ->addColumn('updated', 'integer', ['null' => true])
          ->create()
          ;
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('apikeys');
    }
}
