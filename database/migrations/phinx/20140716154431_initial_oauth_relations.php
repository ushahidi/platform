<?php

use Phinx\Migration\AbstractMigration;

class InitialOauthRelations extends AbstractMigration
{
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

    /**
     * Migrate Up.
     */
    public function up()
    {
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

    /**
     * Migrate Down.
     */
    public function down()
    {
        foreach ($this->foreign_keys as $key) {
            // For dropping, we only need the local table and column
            list($table, $column) = $key;
            $this->table($table)->dropForeignKey($column);
        }
    }
}
