<?php

use Phinx\Migration\AbstractMigration;

class InitialOauth extends AbstractMigration
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
        $this->table('oauth_clients', ['id' => false])
            ->addColumn('id', 'string', ['limit' => 40])
            ->addColumn('secret', 'string', ['limit' => 40])
            ->addColumn('name', 'string')
            ->addColumn('auto_approve', 'boolean', ['default' => 0])
            ->addIndex(['id'], ['unique' => true])
            ->addIndex(['secret', 'id'], ['unique' => true])
            ->create();

        $this->table('oauth_client_endpoints')
            ->addColumn('client_id', 'string', ['limit' => 40])
            ->addColumn('redirect_uri', 'string')
            ->create();

        $this->table('oauth_sessions')
            ->addColumn('client_id', 'string', ['limit' => 40])
            ->addColumn('owner_type', 'string', [
                'default' => 'user',
                'comment' => 'user, client',
                ])
            ->addColumn('owner_id', 'string')
            ->addIndex(['owner_type'])
            ->addIndex(['owner_id'])
            ->create();

        $this->table('oauth_session_access_tokens')
            ->addColumn('session_id', 'integer')
            ->addColumn('access_token', 'string', ['limit' => 40])
            ->addColumn('access_token_expires', 'integer')
            ->addIndex(['access_token', 'session_id'], ['unique' => true])
            ->create();

        $this->table('oauth_session_authcodes')
            ->addColumn('session_id', 'integer')
            ->addColumn('auth_code', 'string', ['limit' => 40])
            ->addColumn('auth_code_expires', 'integer')
            ->create();

        $this->table('oauth_session_redirects', ['id' => false])
            ->addColumn('session_id', 'integer')
            ->addColumn('redirect_uri', 'string')
            ->create();

        $this->table('oauth_session_refresh_tokens', [
                'id' => false,
                'primary_key' => 'session_access_token_id',
                ])
            ->addColumn('session_access_token_id', 'integer')
            ->addColumn('refresh_token', 'string', ['limit' => 40])
            ->addColumn('refresh_token_expires', 'integer')
            ->addColumn('client_id', 'string', ['limit' => 40])
            ->create();

        $this->table('oauth_scopes')
            ->addColumn('scope', 'string')
            ->addColumn('name', 'string')
            ->addColumn('description', 'string', ['null' => true])
            ->addIndex(['scope'], ['unique' => true])
            ->create();

        $this->table('oauth_session_token_scopes')
            // OAuth2 Server wants this to be a "bigint" column, but Phinx does
            // not allow ['id' => 'id'] definitions, but there is a PR to fix it:
            // https://github.com/robmorgan/phinx/pull/158
            // ->addColumn('id', 'biginteger')
            ->addColumn('session_access_token_id', 'integer', ['null' => true])
            ->addColumn('scope_id', 'integer', ['null' => true])
            ->addIndex(['session_access_token_id', 'scope_id'], ['unique' => true])
            ->create();

        $this->table('oauth_session_authcode_scopes', [
                'id' => false,
                'primary_key' => ['oauth_session_authcode_id', 'scope_id'],
                ])
            ->addColumn('oauth_session_authcode_id', 'integer')
            ->addColumn('scope_id', 'integer')
            ->create();
    }

    /**
     * Migrate Up.
     */
    public function up()
    {
        // noop, uses change()
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // noop, uses change()
    }
}
