<?php

use Phinx\Migration\AbstractMigration;

class DropOldOauthTables extends AbstractMigration
{
    /**
     * Drop Oauth tables
     */
    public function up() {
        $this->dropTable('oauth_session_refresh_tokens');
        $this->dropTable('oauth_session_token_scopes');
        $this->dropTable('oauth_session_authcode_scopes');
        $this->dropTable('oauth_session_access_tokens');
        $this->dropTable('oauth_session_authcodes');
        $this->dropTable('oauth_session_redirects');
        $this->dropTable('oauth_sessions');
        $this->dropTable('oauth_client_endpoints');
        $this->dropTable('oauth_clients');
        $this->dropTable('oauth_scopes');
    }

    /**
     * Recreate oauth tables
     */
    public function down()
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


        // The default client is treated as a public client, and is restricted
        // by endpoint, not the secret.
        $secret = sha1('ushahidiui');
        $this->execute(
            "INSERT INTO oauth_clients (id, secret, name, auto_approve)
            VALUES (
                'ushahidiui',
                '$secret',
                'Ushahidi Platform Client',
                1
            )"
        );

        // The redirect_uri restriction here should be in sync with the client configuration.
        $this->execute(
            "INSERT INTO oauth_client_endpoints (client_id, redirect_uri)
            VALUES (
                'ushahidiui',
                '/user/oauth'
            )"
        );

        // Insert scopes
         $this->execute(
            "
            INSERT INTO `oauth_scopes` (`id`, `scope`, `name`, `description`)
            VALUES
                (1,'api','api',NULL),
                (2,'posts','posts',NULL),
                (3,'forms','forms',NULL),
                (4,'sets','set',NULL),
                (5,'tags','tags',NULL),
                (6,'users','users',NULL),
                (7,'media','media',NULL),
                (8,'config','config',NULL),
                (9,'messages','messages',NULL),
                (10,'dataproviders','dataproviders',NULL),
                (11,'stats','stats',NULL),
                (12,'layers','layers',NULL),
                (13,'savedsearches','savedsearches',NULL),
                (14,'notifications','notifications',NULL),
                (15,'contacts','contacts',NULL),
                (16,'csv','csv',NULL),
                (17,'roles','roles',NULL),
                (18,'permissions','permissions',NULL),
                (19,'migrate','migrate',NULL),
                (20,'webhooks','webhooks',NULL);
            ");

        // Add relations
        foreach ($this->foreign_keys as $key) {
            list($ltable, $lcolumn, $rtable, $rcolumn) = $key;
            try {
                $this->table($ltable)
                     ->addForeignKey($lcolumn, $rtable, $rcolumn, [
                        'delete' => 'CASCADE',
                        'update' => 'CASCADE',
                        ])
                     ->save();
            } catch (Exception $e) {
                throw new Exception(
                    "Failed to add foreign key: $ltable.$lcolumn -> $rtable.$rcolumn " .
                    $e->getMessage()
                );
            }
        }
    }

    private $foreign_keys = [
        // Define all foreign keys here, in format:
        // [local table, local column, remote table, remote column]
        ['oauth_client_endpoints', 'client_id', 'oauth_clients', 'id'],
        ['oauth_sessions', 'client_id', 'oauth_clients', 'id'],
        ['oauth_session_access_tokens', 'session_id', 'oauth_sessions', 'id'],
        ['oauth_session_authcodes', 'session_id', 'oauth_sessions', 'id'],
        ['oauth_session_redirects', 'session_id', 'oauth_sessions', 'id'],
        ['oauth_session_refresh_tokens', 'session_access_token_id', 'oauth_session_access_tokens', 'id'],

        ['oauth_session_refresh_tokens', 'client_id', 'oauth_clients', 'id'],
        ['oauth_session_token_scopes', 'session_access_token_id', 'oauth_session_access_tokens', 'id'],
        ['oauth_session_token_scopes', 'scope_id', 'oauth_scopes', 'id'],
        ['oauth_session_authcode_scopes', 'oauth_session_authcode_id', 'oauth_session_authcodes', 'id'],
        ['oauth_session_authcode_scopes', 'scope_id', 'oauth_scopes', 'id'],
        ];
}
