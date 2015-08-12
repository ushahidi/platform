<?php

use Phinx\Migration\AbstractMigration;

class CreateUserResetTokens extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     **/
    public function change()
    {
        $this->table('user_reset_tokens')
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('reset_token', 'string', ['limit' => 40])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                ])
            ->create();
    }
}
