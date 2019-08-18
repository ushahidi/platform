<?php

use Phinx\Migration\AbstractMigration;

class ContactPostState extends AbstractMigration
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
     * with the Table class. targeted_survey_state table: post_id, status, contact_id

     */

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('targeted_survey_state')
            ->addColumn('post_id', 'integer', ['null' => false])
            ->addColumn('contact_id', 'integer', ['null' => false])
            ->addColumn('status', 'string', ['null' => false, 'default' => 'PENDING'])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['default' => 0])
            ->addForeignKey('contact_id', 'contacts', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('post_id', 'posts', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('targeted_survey_state');
    }
}
