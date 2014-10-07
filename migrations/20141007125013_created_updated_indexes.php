<?php

use Phinx\Migration\AbstractMigration;

class CreatedUpdatedIndexes extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     */
    public function change()
    {
        $this->table('config')
            ->addIndex(['updated'])
            ->update();

        $this->table('contacts')
            ->addIndex(['created'])
            ->addIndex(['updated'])
            ->update();

        $this->table('forms')
            ->addIndex(['created'])
            ->addIndex(['updated'])
            ->update();

        $this->table('media')
            ->addIndex(['created'])
            ->addIndex(['updated'])
            ->update();

        $this->table('messages')
            ->addIndex(['created'])
            ->update();

        $this->table('posts')
            ->addIndex(['created'])
            ->addIndex(['updated'])
            ->update();

        $this->table('post_comments')
            ->addIndex(['created'])
            ->addIndex(['updated'])
            ->update();

        $this->table('post_datetime')
            ->addIndex(['created'])
            ->update();

        $this->table('post_decimal')
            ->addIndex(['created'])
            ->update();

        $this->table('post_geometry')
            ->addIndex(['created'])
            ->update();

        $this->table('post_int')
            ->addIndex(['created'])
            ->update();

        $this->table('post_point')
            ->addIndex(['created'])
            ->update();

        $this->table('post_text')
            ->addIndex(['created'])
            ->update();

        $this->table('post_varchar')
            ->addIndex(['created'])
            ->update();

        $this->table('sets')
            ->addIndex(['created'])
            ->addIndex(['updated'])
            ->update();

        $this->table('tags')
            ->addIndex(['created'])
            ->update();

        $this->table('tasks')
            ->addIndex(['created'])
            ->addIndex(['updated'])
            ->update();

        $this->table('users')
            ->addIndex(['created'])
            ->addIndex(['updated'])
            ->update();

        $this->table('layers')
            ->addIndex(['created'])
            ->addIndex(['updated'])
            ->update();

    }
}
