<?php

use Phinx\Migration\AbstractMigration;

class CreateJobsTable extends AbstractMigration
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
        $this->table('jobs')
            ->addColumn('queue', 'string', [
                'null' => false,
                'default' => false
            ])
            ->addColumn(
                'payload',
                'text',
                [
                    'null' => true,
                    'limit' => Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG,
                    'default' => null,
                ]
            )
            ->addColumn(
                'attempts',
                'text',
                [
                    'null' => true,
                    'limit' => Phinx\Db\Adapter\MysqlAdapter::INT_TINY,
                    'default' => null,
                ]
            )
            ->addColumn(
                'reserved_at',
                'text',
                [
                    'null' => true,
                    'limit' => Phinx\Db\Adapter\MysqlAdapter::INT_MEDIUM,
                    'default' => null,
                ]
            )

            ->addColumn(
                'available_at',
                'text',
                [
                    'null' => true,
                    'limit' => Phinx\Db\Adapter\MysqlAdapter::INT_MEDIUM,
                    'default' => null,
                ]
            )
            ->addColumn(
                'created_at',
                'text',
                [
                    'null' => true,
                    'limit' => Phinx\Db\Adapter\MysqlAdapter::INT_MEDIUM,
                    'default' => null,
                ]
            )
            ->addIndex(['queue'])
            ->create();
    }
    public function down()
    {
        $this->dropTable('jobs');
    }
}
