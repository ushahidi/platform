<?php

use Phinx\Migration\AbstractMigration;

class RestoreDefaultOauthClient extends AbstractMigration
{
    /**
     * Add ushahidiui client
     */
    public function up()
    {
        // The default client is treated as a public client, and is restricted
        // by endpoint, not the secret.
        $secret = sha1('ushahidiui');
        $this->execute(
            "INSERT INTO oauth_clients (id, secret, name, password_client, personal_access_client, revoked, created_at, updated_at, redirect)
            VALUES (
                'ushahidiui',
                '$secret',
                'Ushahidi Platform Web Client',
                1,
                0,
                0,
                NOW(),
                NOW(),
                'http://localhost'
            )"
        );
    }

    public function down()
    {
        $this->execute(
            "DELETE FROM oauth_clients where id = 'ushahidui'"
        );
    }
}
