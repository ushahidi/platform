<?php

use Phinx\Migration\AbstractMigration;

class RemoveTagsFromPostVarchar extends AbstractMigration
{
    public function up()
    {
        // Remove all tag values from post_varchar
        $this->execute(
            "DELETE from post_varchar
                WHERE form_attribute_id IN
                    (SELECT form_attributes.id FROM form_attributes WHERE input = 'tags' AND type = 'varchar')"
        );
    }

    public function down()
    {
        // No op - not reversible
    }
}
