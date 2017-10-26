<?php

use Phinx\Migration\AbstractMigration;

class CreatePostDataExportTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('postdataexports')
          ->addColumn('user_id', 'integer', ['null' => false])
          ->addColumn('filter', 'string', ['null' => false])
          ->addColumn('filename', 'string', ['null' => true])
          ->addColumn('created', 'integer', ['default' => 0])
          ->addColumn('updated', 'integer', ['default' => 0], ['null' => true])
          ->addForeignKey('user_id', 'users', 'id', [
                    'delete' => 'CASCADE',
                    'update' => 'CASCADE',
                    ])
          ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('postdataexports');
    }
}

