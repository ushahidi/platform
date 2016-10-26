<?php

use Phinx\Migration\AbstractMigration;

class AddBulkOauthRole extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("INSERT INTO oauth_scopes (scope, name) VALUES ('posts_bulk', 'posts_bulk')");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("DELETE FROM oauth_scopes WHERE scope = 'posts_bulk'");
    }
}