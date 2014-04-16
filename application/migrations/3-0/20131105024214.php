<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20131105024214 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// noop: moved to demo-data group
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		// noop: moved to demo-data group
	}

}
