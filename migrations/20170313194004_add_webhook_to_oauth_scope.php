<?php

use Phinx\Migration\AbstractMigration;

class AddWebhookToOauthScope extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        if ($this->hasTable('oauth_scopes')) {
            $this->execute("INSERT INTO oauth_scopes (scope, name) VALUES ('webhooks', 'webhooks')");
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        if ($this->hasTable('oauth_scopes')) {
            $this->execute("DELETE FROM oauth_scopes WHERE scope = 'webhooks'");
        }
    }
}
