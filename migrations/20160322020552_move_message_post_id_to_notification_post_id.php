<?php

use Phinx\Migration\AbstractMigration;

class MoveMessagePostIdToNotificationPostId extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("
            UPDATE messages
            SET notification_post_id = post_id
            WHERE direction = 'outgoing'
            AND post_id IS NOT NULL
        ");
        $this->execute("UPDATE messages
            SET post_id = NULL
            WHERE direction = 'outgoing'
            AND post_id IS NOT NULL
            AND post_id = notification_post_id;
        ");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("
            UPDATE messages
            SET post_id = notification_post_id
            WHERE direction = 'outgoing'
            AND notification_post_id IS NOT NULL
        ");
        $this->execute("UPDATE messages
            SET notification_post_id = NULL
            WHERE direction = 'outgoing'
            AND notification_post_id IS NOT NULL
            AND notification_post_id = post_id;
        ");
    }
}
