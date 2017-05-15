<?php

use Phinx\Migration\AbstractMigration;

class CreateOauthRefreshTokensTable extends AbstractMigration
{
    /**
     * Create oauth_refresh_tokens
     */
    public function change()
    {
        $this->table('oauth_refresh_tokens', [
                'id' => false,
                'primary_key' => 'id',
            ])
            ->addColumn('id', 'string', ['limit' => 100])
            ->addColumn('access_token_id', 'string', ['limit' => 100])
            ->addColumn('revoked', 'boolean', ['default' => 0])
            ->addColumn('expires_at', 'datetime', ['null' => true])
            ->addIndex(['access_token_id'])
            ->create();

    }
}
