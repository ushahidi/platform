<?php

use Phinx\Migration\AbstractMigration;

class AddUserSettingsTable extends AbstractMigration
{
    /**
    * Migrate Up.
    */
    public function up()
    {
        $this->table('user_settings')
            ->addColumn('config_key', 'string', [
                'null' => false,
                'default' => false
            ])
            ->addColumn('config_value', 'string', [
                'null' => false,
                'default' => false
            ])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['default' => 0])
            ->addIndex(
                ['config_key', 'user_id'],
                [
                    'unique' => true
                ]
            )
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE'
            ])
            ->create();
    }

    /**
    * Migrate Down.
    */
    public function down()
    {
        $this->dropTable('user_settings');
    }
}
