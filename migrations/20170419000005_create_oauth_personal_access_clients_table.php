<?php

use Phinx\Migration\AbstractMigration;

class CreateOauthPersonalAccessClientsTable extends AbstractMigration
{
    /**
     * Create oauth_personal_access_clients
     */
    public function change()
    {
        $this->table('oauth_personal_access_clients')
            // Using phinx default ID, so its signed unlike in default passport migrations
            ->addColumn('client_id', 'string', ['limit' => 100])
            ->addColumn('created_at', 'timestamp', ['null' => true])
            ->addColumn('updated_at', 'timestamp', ['null' => true])
            ->addIndex(['client_id'])
            ->create();
    }
}
