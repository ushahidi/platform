<?php

use Phinx\Migration\AbstractMigration;

class RemoveUserEmail extends AbstractMigration
{
    /**
     * Remove user email column
     */
    public function up()
    {
        $this->table('users')
            ->removeColumn('email')
            ->update();
    }

    public function down()
    {
        $this->table('users')
            ->addColumn('email', 'string', [
                'limit' => 150,
                'null' => true,
                'default' => null,
                'after' => 'id'
            ])
            ->update();

        // Repopulate users.email
        $this->execute("
            UPDATE `users`
                JOIN `contacts` ON (
                    `users`.`id` = `contacts`.`user_id`
                    AND `contacts`.`type` = 'email'
                )
                SET `users`.`email` = `contacts`.`contact`
                WHERE `users`.`email` IS NULL;
            ");
    }
}
