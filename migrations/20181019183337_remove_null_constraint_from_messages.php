<?php

use Phinx\Migration\AbstractMigration;

class RemoveNullConstraintFromMessages extends AbstractMigration
{
    public function up()
    {
        $this->table('messages')
            ->changeColumn('message', 'text', ['null' => true])
            ->update();
    }

    public function down()
    {
        $this->table('messages')
            ->changeColumn('message', 'text', ['null' => false])
            ->update();
    }
}
