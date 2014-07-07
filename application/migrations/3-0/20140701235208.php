<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20140701235208 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, 'ALTER TABLE media ADD o_size INT UNSIGNED NOT NULL AFTER o_filename');
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'ALTER TABLE media DROP o_size');
	}

}
