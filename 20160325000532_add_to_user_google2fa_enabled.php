<?php

use Phinx\Migration\AbstractMigration;

class AddToUserGoogle2faEnabled extends AbstractMigration
{
    /**
     * Migrate Up.
     *
     */
    public function up()
    {
        $this->table('users')
            ->addColumn('google2fa_enabled', 'boolean')
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('users')
            ->removeColumn('google2fa_enabled')
            ->update();
    }
}
