<?php

use Phinx\Migration\AbstractMigration;

class AddUseGeolocationToPost extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('posts')
            ->addColumn('use_geolocation', 'boolean', [
                'null' => false,
                'default' => true
                ])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('posts')
            ->removeColumn('use_geolocation')
            ->update();
    }
}
