<?php

use Phinx\Migration\AbstractMigration;

class RemoveTweetContents extends AbstractMigration
{

    public function change()
    {
        $this->execute("
                UPDATE posts 
                    INNER JOIN messages ON posts.id=messages.post_id 
                    SET posts.title = CONCAT('From twitter on ' ,from_unixtime(posts.created, '%Y %D %M %h:%i:%s'))
                    WHERE messages.message=posts.title
                    AND messages.data_source='twitter';
        ");
        $this->execute("
                UPDATE posts 
                    INNER JOIN messages ON posts.id=messages.post_id 
                    SET posts.content = CONCAT('https://twitter.com/statuses/' ,messages.data_source_message_id)
                    WHERE messages.message=posts.content
                    AND messages.data_source='twitter';
        ");
        $this->execute("UPDATE messages set message=null where data_source='twitter'");
    }
}
