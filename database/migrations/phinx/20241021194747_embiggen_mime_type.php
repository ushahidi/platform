<?php

use Phinx\Migration\AbstractMigration;

class EmbiggenMimeType extends AbstractMigration
{
    public function change()
    {
        $this->table('media')
            ->changeColumn('mime', 'string', ['limit' => 128])
            ->update();
    }

    public function down()
    {
    }
}
