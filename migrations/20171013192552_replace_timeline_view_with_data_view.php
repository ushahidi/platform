<?php

use Phinx\Migration\AbstractMigration;

class ReplaceTimelineViewWithDataView extends AbstractMigration
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

	/**
	 * Sets all views to use 'data' instead of 'list' since
	 * we no longer have a 'list' view.
	 */
    public function up()
    {
        $sql = "UPDATE sets SET view='data' WHERE view='list'";
        $this->execute($sql);
    }

	/**
	 * since this migration is only there for when we switch from having a list view to not having one,
	 * the down() script would be adequate. For more advanced situations we can run into a down
	 * setting pre-existing sets with view=data to being sets with view=list. I think that's
	 * acceptable as this is intended for setup.
	 */
    public function down()
    {
		$sql = "UPDATE sets SET view='list' WHERE view='data'";
        $this->execute($sql);
    }
}
