<?php

use Phinx\Migration\AbstractMigration;

class RemoveTweetContents extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
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
