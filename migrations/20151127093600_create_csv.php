<?php

use Phinx\Migration\AbstractMigration;

class CreateCsv extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     **/

	public function change()
    {
        $this->table('csv')
			->addColumn('columns', 'text', ['null' => false])
			 ->addColumn('maps_to', 'text', ['default' => ''])
			 ->addColumn('tags', 'text', , ['default' => ''])
			->addColumn('status', 'string', ['limit' => 20])
			->addColumn('published_to', 'string', ['limit' => 150])
            ->addColumn('form_id', 'integer', ['null' => false])
			->addColumn('filename', 'string', ['null' => false])
			 ->addColumn('size', 'integer', , ['default' => 0])
			->addColumn('mime', 'string', ['limit' => 50])
			->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['null' => true])
			->addColumn('completed', 'boolean', ['default' => false])
			->addForeignKey('form_id', 'forms', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                ])
            ->create()
			;
    }
}
