<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20131204232927 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Add key and icon columns
		$db->query(NULL, "ALTER TABLE `form_groups`
		ADD COLUMN `icon` VARCHAR(100) NOT NULL DEFAULT '';");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		 $db->query(NULL, "ALTER TABLE `form_groups`
		 DROP COLUMN `icon`;");
	}

}
