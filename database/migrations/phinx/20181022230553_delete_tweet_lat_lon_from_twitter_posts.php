<?php

use Phinx\Migration\AbstractMigration;

class DeleteTweetLatLonFromTwitterPosts extends AbstractMigration
{
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
