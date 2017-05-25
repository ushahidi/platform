<?php

use Phinx\Migration\AbstractMigration;

class AddFormsTagsTable extends AbstractMigration
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
        $this->table('forms_tags', [
                'id' => false,
                'primary_key' => ['form_id', 'tag_id'],
                ])
            ->addColumn('form_id', 'integer')
            ->addForeignKey('form_id', 'forms', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE'
                ])
            ->addColumn('tag_id', 'integer')
            ->addForeignKey('tag_id', 'tags', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE'
                ])
            ->create();
    }
}
