<?php

use Phinx\Migration\AbstractMigration;

class AddSavedSearchesOauthScope extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("INSERT INTO oauth_scopes (scope, name) VALUES ('savedsearches', 'saved searches')");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("DELETE FROM oauth_scopes WHERE scope = 'savedsearches'");
    }
}
