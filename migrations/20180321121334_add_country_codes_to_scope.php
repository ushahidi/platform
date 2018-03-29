<?php

use Phinx\Migration\AbstractMigration;

class AddCountryCodesToScope extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
            $this->execute("INSERT INTO oauth_scopes (scope, name) VALUES ('country_codes', 'country_codes')");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
            $this->execute("DELETE FROM oauth_scopes WHERE scope = 'country_codes'");
    }
}
