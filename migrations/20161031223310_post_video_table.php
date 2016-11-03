<?php

use Phinx\Migration\AbstractMigration;

class PostVideoTable extends AbstractMigration
{
    public function change()
    {
        $this->table('post_video')
             ->addColumn('post_id', 'integer')
             ->addColumn('form_attribute_id', 'integer')
             ->addColumn('value', 'string', ['null' => true])
             ->addColumn('created', 'integer', ['default' => 0])
             ->addColumn('updated', 'integer', ['default' => 0])
             ->addIndex(['created'])
             ->addForeignKey('form_attribute_id', 'form_attributes', 'id', [
                 'delete' => 'CASCADE',
             ])
             ->create();
    }
}
