<?php

use Phinx\Migration\AbstractMigration;

class AllowNullPostTitle extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('posts')
            ->changeColumn('title', 'string', [
                'limit' => 150,
                'null' => true,
                ])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('posts')
            ->changeColumn('title', 'string', [
                'limit' => 150,
                'null' => false,
                ])
            ->update();
    }
}
