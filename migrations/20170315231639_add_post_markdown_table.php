<?php

use Phinx\Migration\AbstractMigration;

class AddPostMarkdownTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('post_markdown')
          ->addColumn('post_id', 'integer')
          ->addColumn('form_attribute_id', 'integer')
          ->addColumn('value', 'text', ['null' => true])
          ->addColumn('created', 'integer', ['default' => 0])
          ->addColumn('updated', 'integer', ['null' => true])
          ->addForeignKey('form_attribute_id', 'form_attributes', 'id', [
              'delete' => 'CASCADE',
              'update' => 'CASCADE',
          ])
          ->addForeignKey('post_id', 'posts', 'id', [
              'delete' => 'CASCADE',
              'update' => 'CASCADE',
          ])
          ->create()
          ;
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('post_markdown');
    }
}
