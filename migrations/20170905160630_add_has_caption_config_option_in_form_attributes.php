<?php

use Phinx\Migration\AbstractMigration;

class AddHasCaptionConfigOptionInFormAttributes extends AbstractMigration
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
		$config = json_encode(["hasCaption"=> true]);
    	$sql = "UPDATE form_attributes SET config='$config' WHERE (config IS NULL or config='[]') and type='media'";
    	$this->execute($sql);
	}

	public function down()
	{
		$config = json_encode(["hasCaption"=> true]);
		$sql = "UPDATE form_attributes SET config='[]' WHERE config='$config' and type='media';";
		$this->execute($sql);
	}
}
