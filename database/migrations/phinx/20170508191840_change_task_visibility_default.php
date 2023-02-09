<?php

use Phinx\Migration\AbstractMigration;

class ChangeTaskVisibilityDefault extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('form_stages')
            ->changeColumn('show_when_published', 'boolean', [
                'null' => false,
                'default'=> true
                ])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('form_stages')
            ->changeColumn('show_when_published', 'boolean', [
                'null' => true,
                'default'=> true
                ])
            ->update();
    }
}
