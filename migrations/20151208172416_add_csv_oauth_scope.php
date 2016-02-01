<?php

use Phinx\Migration\AbstractMigration;

class AddCsvOauthScope extends AbstractMigration
{
	/**
     * Migrate Up.
     */
    public function up()
    {
		$this->execute("INSERT INTO oauth_scopes (scope, name) VALUES ('csv', 'csv')");

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
		$this->execute("DELETE FROM oauth_scopes WHERE scope = 'csv'");
    }
}
