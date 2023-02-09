<?php

use Phinx\Migration\AbstractMigration;

class DefaultOauthClient extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        // The default client is treated as a public client, and is restricted
        // by endpoint, not the secret.
        $secret = sha1('ushahidiui');

        $this->execute(
            "INSERT INTO oauth_clients (id, secret, name)
            VALUES (
                'ushahidiui',
                '$secret',
                'Ushahidi Platform Client'
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
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("DELETE FROM oauth_clients WHERE id = 'ushahidiui'");
    }
}
