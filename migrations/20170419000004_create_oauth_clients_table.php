<?php

use Phinx\Migration\AbstractMigration;

class CreateOauthClientsTable extends AbstractMigration
{
    /**
     * Create oauth_clients
     */
    public function change()
    {
        $this->table('oauth_clients', [
                'id' => false,
                'primary_key' => 'id',
            ])
            // This deviates from passport to use a string ID
            ->addColumn('id', 'string', ['limit' => 100])
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('name', 'string')
            ->addColumn('secret', 'string', ['limit' => 100])
            ->addColumn('redirect', 'text')
            ->addColumn('personal_access_client', 'boolean', ['default' => 0])
            ->addColumn('password_client', 'boolean', ['default' => 0])
            ->addColumn('revoked', 'boolean', ['default' => 0])
            ->addColumn('created_at', 'timestamp', ['null' => true])
            ->addColumn('updated_at', 'timestamp', ['null' => true])
            ->addIndex(['user_id'])
            ->create();
    }
}
