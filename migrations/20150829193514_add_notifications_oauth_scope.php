<?php

use Phinx\Migration\AbstractMigration;

class AddNotificationsOauthScope extends AbstractMigration
{
    
    /**
     * Migrate Up.
     */
    public function up()
    {
		$this->execute("INSERT INTO oauth_scopes (scope, name) VALUES ('notifications', 'notifications')");

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
		$this->execute("DELETE FROM oauth_scopes WHERE scope = 'notifications'");
    }
}
