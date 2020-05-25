<?php

use Phinx\Migration\AbstractMigration;

class AddMultipleValuesAttributesToCsv extends AbstractMigration
{
    public function up()
    {
        $this->table('csv')
            ->addColumn('multiple_values_attributes', 'text', ['null' => true])
            ->update();
    }

    public function down()
    {
        $this->table('csv')->removeColumn('multiple_values_attributes')->update();
    }
}
