<?php

use Phinx\Migration\AbstractMigration;

class AddLayers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
     */
    public function change()
    {
        $this->table('layers')
            ->addColumn('media_id', 'integer', ['null' => true])
            ->addColumn('name', 'string', ['limit' => 50])
            ->addColumn('type', 'string', [
                'limit' => 20,
                'default' => 'geojson',
                'comment' => 'geojson, wms, tile',
                ])
            ->addColumn('data_url', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('options', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('active', 'integer', ['limit' => 1, 'default' => 1])
            ->addColumn('visible_by_default', 'integer', ['limit' => 1, 'default' => 1])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['default' => 0])
            ->addForeignKey('media_id', 'media', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
                ])
            ->create();
    }


    /**
     * Migrate Up.
     */
    public function up()
    {
        // noop, uses change()
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // noop, uses change()
    }
}
