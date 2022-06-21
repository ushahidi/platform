<?php

use Phinx\Migration\AbstractMigration;

class MakePostTitleLonger extends AbstractMigration
{
    public function up()
    {
        $this->table('posts')
            ->changeColumn('title', 'string', ['limit' => 255, 'null' => true])
            ->update();
    }

    public function down()
    {
        // No op, don't truncate titles
    }
}
