<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20140310135457 extends Minion_Migration_Base {


	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// noop - moved to koauth module
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		// noop - moved to koauth module
	}

}
