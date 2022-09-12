<?php

use Phinx\Migration\AbstractMigration;
use \Phinx\Db\Adapter\MysqlAdapter;

class CreateLicenseTable extends AbstractMigration
{
    public function up()
    {
        $this->table('hxl_license')
            ->addColumn('name', 'string', [
                'null' => false,
                'default' => false
            ])
            ->addColumn('link', 'string', [
                'null' => false,
                'default' => false
            ])
            ->addColumn('code', 'string', [
                'null' => false,
                'default' => false
            ])
            ->addIndex(['name'])
            ->addIndex(['code'], ['unique' => true])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('hxl_license');
    }
}
