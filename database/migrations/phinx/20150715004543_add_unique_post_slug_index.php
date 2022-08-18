<?php

use Phinx\Migration\AbstractMigration;

class AddUniquePostSlugIndex extends AbstractMigration
{
    /**
     * Add unique index on slug
     */
    public function change()
    {
        $this->table('posts')
            ->addIndex('slug', ['unique' => true])
            ->update();
    }
}
