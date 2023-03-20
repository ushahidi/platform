<?php

use Phinx\Migration\AbstractMigration;

class FormsSoftDelete extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("ALTER TABLE forms ADD COLUMN `disabled` TINYINT(1) NOT NULL DEFAULT 0;");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("ALTER TABLE forms DROP COLUMN `disabled`;");
    }
}
