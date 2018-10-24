<?php

use Phinx\Migration\AbstractMigration;

class RemoveNullConstraintFromMessages extends AbstractMigration
{
    public function change()
    {
        $this->table('messages')
            ->changeColumn('message', 'text', ['null' => true])
            ->update();
    }
}
