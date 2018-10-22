<?php

use Phinx\Migration\AbstractMigration;

class DeleteTweetLatLonFromTwitterPosts extends AbstractMigration
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
        $this->execute(
            "DELETE FROM post_point WHERE form_attribute_id IN 
                    (SELECT id from form_attributes where `key`='message_location')"
        );

        $this->execute(
            "UPDATE messages SET additional_data='[]' WHERE data_source='twitter'"
        );

    }
}