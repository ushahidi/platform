<?php

use Phinx\Migration\AbstractMigration;

class AddUseGeolocationToFormAttributes extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('form_attributes')
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
        $this->table('form_attributes')
            ->removeColumn('use_geolocation')
            ->update();
    }
}
