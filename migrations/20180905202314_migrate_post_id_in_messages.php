<?php

use Phinx\Migration\AbstractMigration;

class MigratePostIdInMessages extends AbstractMigration
{
    public function change()
    {
        $update = "UPDATE messages INNER JOIN posts on posts.content=messages.message SET " .
            "messages.post_id=posts.id WHERE messages.post_id IS NULL and " .
            "messages.created>UNIX_TIMESTAMP(STR_TO_DATE('2018-08-29', '%Y-%m-%d'));";
        $this->execute($update);
    }
}
