<?php

use Phinx\Migration\AbstractMigration;

class RemoveTagsFromTasks extends AbstractMigration
{
    public function up()
    {
         $this->execute("
            DELETE FROM `form_attributes` WHERE
            `input` = 'tags' AND
            `form_stage_id` IN (SELECT `id` FROM `form_stages` WHERE `type` = 'task')
        ");
    }

    public function down()
    {
        // No op, not reversible
    }
}
