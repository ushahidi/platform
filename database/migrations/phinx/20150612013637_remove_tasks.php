<?php

use Phinx\Migration\AbstractMigration;

class RemoveTasks extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->dropTable('tasks');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('tasks')
            ->addColumn('parent_id', 'integer', ['null' => true])
            ->addColumn('post_id', 'integer', ['null' => true])
            ->addColumn('assignee', 'integer', ['null' => true])
            ->addColumn('assignor', 'integer', ['null' => true])
            ->addColumn('description', 'string')
            ->addColumn('status', 'string', [
                'limit' => 20,
                'default' => 'pending',
                'comment' => 'pending, complete, later',
                ])
            ->addColumn('due', 'integer', ['null' => true])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['null' => true])
            ->create();
    }
}
