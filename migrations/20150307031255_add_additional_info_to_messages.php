<?php

use Phinx\Migration\AbstractMigration;

class AddAdditionalInfoToMessages extends AbstractMigration
{

    public function change()
    {
        $this->table('messages')
            ->addColumn('additional_data', 'text', ['null' => true])
            ->update();
    }
}
