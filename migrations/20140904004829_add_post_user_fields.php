<?php

use Phinx\Migration\AbstractMigration;

class AddPostUserFields extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     **/
    public function change()
    {
        $this->table('posts')
            ->addColumn('author_realname', 'string', [
                'after' => 'content',
                'limit' => 150,
                'null' => true,
                ])
            ->addColumn('author_email', 'string', [
                'after' => 'content',
                'limit' => 150,
                'null' => true,
                ])
            ->addIndex(['author_email'])
            ->update();
    }
}
