<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class ExportJobFieldSize extends AbstractMigration
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
		/**
		 * Changing fields and filters to the MEDIUM_TEXT
		 * length. We would not (always) be able to handle
		 * very large deployments with many fields
		 * with a 255 varchar.
		 */
		$this->table('export_job')
			->changeColumn(
				'fields',
				'text',
				['null' => true, 'limit' => MysqlAdapter::TEXT_MEDIUM, 'default' => null]
			)
			->changeColumn(
				'filters',
				'text',
				['null' => true, 'limit' => MysqlAdapter::TEXT_MEDIUM, 'default' => null]
			)
			->update();
    }
    public function down()
	{
		// Noop - don't rollback because it would truncate use data
	}
}
