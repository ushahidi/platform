<?php

use Phinx\Migration\AbstractMigration;

class CreatePhoneFieldTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('post_phone')
            ->addColumn('post_id', 'integer')
            ->addColumn('form_attribute_id', 'integer')
            ->addColumn('value', 'string', [
               'limit' => 32,
               ])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addForeignKey('post_id', 'posts', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
    }
}
