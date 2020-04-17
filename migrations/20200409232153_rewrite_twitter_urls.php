<?php

use Phinx\Migration\AbstractMigration;

class RewriteTwitterUrls extends AbstractMigration
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
    public function up()
    {
        $sql = "UPDATE messages INNER JOIN contacts on contacts.id = messages.contact_id " .
                "SET messages.message = " .
                "REPLACE(messages.message, concat('https://twitter.com/statuses/', messages.data_source_message_id), " .
                "concat('https://twitter.com/', contacts.contact, '/status/', messages.data_source_message_id)) " .
                "WHERE `messages`.`type` = 'twitter'";
        $this->execute($sql);
    }

    public function down()
    {
        // phpcs:ignore
        $sql = "UPDATE messages INNER JOIN contacts on contacts.id = messages.contact_id " .
                "SET messages.message = REPLACE(messages.message, " .
                "concat('https://twitter.com/', contacts.contact, '/status/', messages.data_source_message_id), " .
                "concat('https://twitter.com/statuses/', messages.data_source_message_id)) " .
                "WHERE `messages`.`type` = 'twitter'";
        $this->execute($sql);
    }
}
