<?php

use Phinx\Migration\AbstractMigration;

class AddLanguageToUser extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('users')
            ->addColumn('language', 'string', [
                'null' => true,
            ])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('users')
            ->removeColumn('language')
            ->update();
    }
}
