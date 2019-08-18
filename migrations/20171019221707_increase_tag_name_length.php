<?php

use Phinx\Migration\AbstractMigration;

class IncreaseTagNameLength extends AbstractMigration
{
    public function up()
    {
        $this->table('tags')
          ->changeColumn('tag', 'string', ['limit' => 255])
          ->changeColumn('slug', 'string', ['limit' => 255])
          ->save();
    }

    public function down()
    {
        $this->table('tags')
          ->changeColumn('tag', 'string', ['limit' => 55])
          ->changeColumn('slug', 'string', ['limit' => 55])
          ->save();
    }
}
