<?php

use Phinx\Migration\AbstractMigration;

class RemoveContactUsernames extends AbstractMigration
{
    public function change()
    {
        $this->execute(
            "DELETE FROM contacts where id IN (select contact_id from messages where data_source='twitter')"
        );
    }
}
