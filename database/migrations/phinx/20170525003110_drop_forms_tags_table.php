<?php

use Phinx\Migration\AbstractMigration;

class DropFormsTagsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        $this->dropTable('forms_tags');
    }

    public function down()
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
