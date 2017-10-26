<?php

use Phinx\Migration\AbstractMigration;

class AddIndicesForPostSearch extends AbstractMigration
{
    public function up()
    {
        $messages = $this->table('messages');
        // Add index for post_id and type since we're now always filtering on message type
        $messages->addIndex(['post_id', 'type'], ['name' => 'post_id_type'])->update();

        // Keep an index on just post_id too
        if (!$messages->hasIndex(['post_id'])) {
            $messages->addIndex(['post_id']);
        }

        $messages->update();

        $this->table('posts')
            // Add index for post_date since we sometimes sort by it
            ->addIndex(['post_date'])
            ->update();
    }

    public function down()
    {
        $messages = $this->table('messages')
            // Add index for post_id and type since we're now always filtering on message type
            ->removeIndex(['post_id', 'type'], ['name' => 'post_id_type'])
            ->update();

        $this->table('posts')
            // Add index for post_date since we sometimes sort by it
            ->removeIndex(['post_date'])
            ->update();
    }
}
