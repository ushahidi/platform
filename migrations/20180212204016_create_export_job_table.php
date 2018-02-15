<?php

use Phinx\Migration\AbstractMigration;

class CreateExportJobTable extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('export_job')
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('entity_type', 'string', ['null' => false])
            ->addColumn('fields', 'string', ['null' => true])
            ->addColumn('filters', 'string', ['null' => true])
            ->addColumn('status', 'string', ['null' => true])
            ->addColumn('url', 'string', ['null' => true])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['default' => 0])
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
        $this->dropTable('export_job');
    }
}
