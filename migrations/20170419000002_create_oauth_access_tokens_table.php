<?php

use Phinx\Migration\AbstractMigration;

class CreateOauthAccessTokensTable extends AbstractMigration
{
    /**
     * Create oauth_access_tokens
     */
    public function change()
    {
        $this->table('oauth_access_tokens', [
                'id' => false,
                'primary_key' => 'id',
            ])
            ->addColumn('id', 'string', ['limit' => 100])
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('client_id', 'string', ['limit' => 100])
            ->addColumn('name', 'string', ['null' => true])
            ->addColumn('scopes', 'text', ['null' => true])
            ->addColumn('revoked', 'boolean')
            ->addColumn('expires_at', 'datetime', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['null' => true])
            ->addColumn('updated_at', 'timestamp', ['null' => true])
            ->create();
    }
}
