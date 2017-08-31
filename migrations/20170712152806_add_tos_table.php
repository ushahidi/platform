<?php

use Phinx\Migration\AbstractMigration;

class AddTosTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('tos')
          ->addColumn('user_id', 'integer', ['null' => false])
          ->addColumn('agreement_date', 'integer', ['null' => false])
          ->addColumn('tos_version_date', 'integer', ['null' => false])
          ->addForeignKey('user_id', 'users', 'id', [
                    'delete' => 'CASCADE',
                    'update' => 'CASCADE',
                    ])
          ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('tos');
    }
}
