<?php

use Phinx\Migration\AbstractMigration;

class MakeTagsTopLevelWhenParentDeleted extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('tags')
            ->dropForeignKey('parent_id')
            ->update();

        $this->table('tags')
            ->addForeignKey('parent_id', 'tags', 'id', [
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
        $this->table('tags')
            ->dropForeignKey('parent_id')
            ->update();

        $this->table('tags')
            ->addForeignKey('parent_id', 'tags', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->update();
    }
}
