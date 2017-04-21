<?php

use Phinx\Migration\AbstractMigration;

class CreateOauthAuthCodesTable extends AbstractMigration
{
    /**
     * Create oauth_auth_codes
     */
    public function change()
    {
        $this->table('oauth_auth_codes', [
                'id' => false,
                'primary_key' => 'id',
            ])
            ->addColumn('id', 'string', ['limit' => 100])
            ->addColumn('user_id', 'integer')
            ->addColumn('client_id', 'string', ['limit' => 100])
            ->addColumn('scopes', 'text', ['null' => true])
            ->addColumn('revoked', 'boolean')
            ->addColumn('expires_at', 'datetime', ['null' => true])
            ->create();
    }
}
