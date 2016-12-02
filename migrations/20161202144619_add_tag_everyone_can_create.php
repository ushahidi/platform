<?php

use Phinx\Migration\AbstractMigration;

class AddTagEveryoneCanCreate extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('tags')
            ->addColumn('everyone_can_create', 'boolean', [
                'default' => false,
            ])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
         $this->table('tags')
             ->removeColumn('everyone_can_create')
             ->update();
    }
}
