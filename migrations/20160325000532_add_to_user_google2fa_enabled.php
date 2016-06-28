<?php

use Phinx\Migration\AbstractMigration;

class AddToUserGoogle2faEnabled extends AbstractMigration
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
        $this->table('users')
            ->addColumn('google2fa_enabled', 'boolean', [
              'default' => false
            ])
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
