<?php

use Phinx\Migration\AbstractMigration;

class AddTosToScope extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
            $this->execute("INSERT INTO oauth_scopes (scope, name) VALUES ('tos', 'tos')");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
            $this->execute("DELETE FROM oauth_scopes WHERE scope = 'tos'");
    }
}
