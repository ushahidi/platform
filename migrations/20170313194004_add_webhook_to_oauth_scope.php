<?php

use Phinx\Migration\AbstractMigration;

class AddWebhookToOauthScope extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
		    $this->execute("INSERT INTO oauth_scopes (scope, name) VALUES ('webhooks', 'webhooks')");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
		    $this->execute("DELETE FROM oauth_scopes WHERE scope = 'webhooks'");
    }
}
