<?php

use Phinx\Migration\AbstractMigration;

class MoveUserEmailToContacts extends AbstractMigration
{
    /**
     * Move user email to contacts
     */
    public function up()
    {
        $this->execute("
            INSERT INTO `contacts` (
                `user_id`,
                `contact`,
                `type`,
                `data_source`,
                `can_notify`,
                `created`
            )
            SELECT
                `id` as `user_id`,
                `email` as `contact`,
                'email' AS `type`,
                'email' AS `data_source`,
                1 AS `can_notify`,
                UNIX_TIMESTAMP() as `created`
            FROM `users`
            WHERE `users`.`email` NOT IN (
                SELECT `contacts`.`contact` FROM `contacts` WHERE `contacts`.`user_id` = `users`.`id`
            );
        ");
    }

    public function down()
    {
        // No need to reverse this
    }
}
