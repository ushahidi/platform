<?php

use Phinx\Migration\AbstractMigration;

class AddTosToScope extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        // noop, scopes table has been removed
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // noop, scopes table has been removed
    }
}
