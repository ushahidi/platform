<?php

use Phinx\Migration\AbstractMigration;

class AddPermissionsOauthScope extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("INSERT INTO oauth_scopes (scope, name) VALUES ('permissions', 'permissions')");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("DELETE FROM oauth_scopes WHERE scope = 'permissions'");
    }
}
