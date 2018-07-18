<?php

use Phinx\Migration\AbstractMigration;

class RemoveMaintainerFromHxlMetaData extends AbstractMigration
{
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('hxl_meta_data');
        $table->removeColumn('maintainer')
              ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('hxl_meta_data');
        $table->addColumn('maintainer', 'string', [
            'null' => false,
            'limit' => 255
        ])
              ->update();
    }
}
