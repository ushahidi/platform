<?php

use Phinx\Migration\AbstractMigration;

class AddRolesOauthScope extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("INSERT INTO oauth_scopes (scope, name) VALUES ('roles', 'roles')");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("DELETE FROM oauth_scopes WHERE scope = 'roles'");
    }
}
