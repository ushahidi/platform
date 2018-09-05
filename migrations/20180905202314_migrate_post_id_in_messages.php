<?php

use Phinx\Migration\AbstractMigration;

class MigratePostIdInMessages extends AbstractMigration
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
        $update = "UPDATE messages INNER JOIN posts on posts.content=messages.message SET " .
            "messages.post_id=posts.id WHERE messages.post_id IS NULL and " .
            "messages.created>UNIX_TIMESTAMP(STR_TO_DATE('2018-08-29', '%Y-%m-%d'));";
        $this->execute($update);
    }
}
