<?php

use Phinx\Migration\AbstractMigration;

class CreateUserGoogle2faSecrets extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    */
    public function change()
    {
        $this->table('user_google2fa_secrets')
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('google2fa_secret', 'string', ['limit' => 60])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                ])
            ->create();
    }
}
