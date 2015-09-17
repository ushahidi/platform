<?php

use Phinx\Migration\AbstractMigration;

class AddSuperpowersToClient extends AbstractMigration
{
    /**
     * Change Method.
     **/
    public function change()
    {
        $this->table('oauth_clients')
            ->addColumn('superpowers', 'boolean', [
                'default' => false,
                'null' => false
                ])
            ->update();
    }
}
