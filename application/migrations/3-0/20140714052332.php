<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20140714052332 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `tags`
		 ADD COLUMN `role` VARCHAR(127) NULL
		 ");
	}

	/*y
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `tags`
		 DROP `role`
		 ");
	}

}
