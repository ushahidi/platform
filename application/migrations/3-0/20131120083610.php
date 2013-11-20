<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20131120083610 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `sets`
			ADD  `state` VARCHAR( 20 ) NOT NULL DEFAULT  'private'
			COMMENT  'public, private' AFTER  `filter`;
		");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `sets` DROP COLUMN `state`;");
	}

}
