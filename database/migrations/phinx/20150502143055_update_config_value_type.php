<?php

use Phinx\Migration\AbstractMigration;

class UpdateConfigValueType extends AbstractMigration
{
    /**
    * Transform config_value into a TEXT column, so we can properly
    * store our longer JSON values.
    */

    // Migrate up. Transform into TEXT column.
    public function up()
    {
        $this->execute("ALTER TABLE config MODIFY config_value TEXT");
    }

    // Migrate down. Transform back to VARCHAR.
    public function down()
    {
        $this->execute("ALTER TABLE config MODIFY config_value VARCHAR(255)");
    }
}
