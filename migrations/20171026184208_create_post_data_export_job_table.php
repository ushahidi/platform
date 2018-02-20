<?php

use Phinx\Migration\AbstractMigration;

class CreatePostDataExportJobTable extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('postdataexport_job')
  		    ->addColumn('postdataexport_id', 'integer', ['null' => false])
    		->addColumn('created', 'integer', ['default' => 0])
    		->addForeignKey('postdataexport_id', 'postdataexports', 'id', [
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
        $this->dropTable('postdataexport_job');
    }
}
