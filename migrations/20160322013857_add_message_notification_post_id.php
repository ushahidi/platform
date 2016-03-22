<?php

use Phinx\Migration\AbstractMigration;

class AddMessageNotificationPostId extends AbstractMigration
{
    /**
     * Change Method.
     **/
    public function change()
    {
        $this->table('messages')
            ->addColumn('notification_post_id', 'integer', [
                    'comment' => "Source post this message is a notification for",
                    'null' => true,
                ])
            ->addForeignKey('notification_post_id', 'posts', 'id', [
                'delete' => 'SET NULL',
                'update' => 'CASCADE',
                ])
            ->update();
    }
}
