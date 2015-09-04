<?php

use Phinx\Migration\AbstractMigration;

class AddContactOauthScope extends AbstractMigration
{
	/**
     * Migrate Up.
     */
    public function up()
    {
		$this->execute("INSERT INTO oauth_scopes (scope, name) VALUES ('contacts', 'contacts')");

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
		$this->execute("DELETE FROM oauth_scopes WHERE scope = 'contacts'");
    }
}
