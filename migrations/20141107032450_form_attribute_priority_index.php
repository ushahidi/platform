<?php

use Phinx\Migration\AbstractMigration;

class FormAttributePriorityIndex extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('form_attributes')->addIndex(['priority'])->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('form_attributes')->removeIndex(['priority']);
    }
}
