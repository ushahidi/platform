<?php

use Phinx\Migration\AbstractMigration;

class AddFormEveryoneCanCreate extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('forms')
            ->addColumn('everyone_can_create', 'boolean', [
                'after' => 'require_approval',
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
        $this->table('forms')
             ->removeColumn('everyone_can_create')
             ->update();
    }
}
