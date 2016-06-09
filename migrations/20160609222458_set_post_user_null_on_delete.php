<?php

use Phinx\Migration\AbstractMigration;

class SetPostUserNullOnDelete extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('posts')
            ->dropForeignKey('user_id')
            ->update();

        $this->table('posts')
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
            ])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('posts')
            ->dropForeignKey('user_id')
            ->update();

        $this->table('posts')
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->update();
    }
}
