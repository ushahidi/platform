<?php

use Phinx\Migration\AbstractMigration;

class AddFormTimeAndLocationPermissions extends AbstractMigration
{
    /**
     * Change Method.
     */
    public function change()
    {
        $this->table('forms')
          ->addColumn('hide_time', 'boolean', [
                'default' => false,
                'null' => false,
            ])
          ->addColumn('hide_location', 'boolean', [
                'default' => false,
                'null' => false,
            ])
          ->update();
    }
}
