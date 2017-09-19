<?php

use Phinx\Migration\AbstractMigration;

class AllowNullInDescriptionForms extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('forms')
            ->changeColumn('description', 'text', ['null' => true])
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('forms')
            ->changeColumn('description', 'text', ['null' => false])
            ->save();
    }
}
