<?php

use Phinx\Migration\AbstractMigration;

class SetConfigUpdatedDefaultValue extends AbstractMigration
{
    public function up()
    {
        $this->table('config')
            ->changeColumn('updated', 'timestamp', [
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP'
            ])
            ->update();
    }

    public function down()
    {
        $this->table('config')
            ->changeColumn('updated', 'timestamp')
            ->update();
    }
}
