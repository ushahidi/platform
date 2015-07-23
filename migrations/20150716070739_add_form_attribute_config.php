<?php

use Phinx\Migration\AbstractMigration;

class AddFormAttributeConfig extends AbstractMigration
{
    /**
     * Add columns to form_attributes table
     */
    public function up()
    {
        $this->table('form_attributes')
            ->addColumn('instructions', 'text', [
                'after' => 'label',
                'null' => true
            ])
            ->addColumn('config', 'text', [
                'after' => 'cardinality',
                'null' => true
            ])
            ->changeColumn('options', 'text', [
                'null' => true
                ])
            ->update();
    }

    public function down()
    {
        $this->table('form_attributes')
            ->removeColumn('instructions')
            ->removeColumn('config')
            ->changeColumn('options', 'string', [
                'null' => true
                ])
            ->update();
    }
}
