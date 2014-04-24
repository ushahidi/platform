<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20140422190955 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, 'ALTER TABLE users CHANGE first_name realname VARCHAR(150) NULL DEFAULT NULL');
		$db->query(NULL, 'UPDATE users SET realname = CONCAT_WS(" ", realname, last_name)');
		$db->query(NULL, 'ALTER TABLE users DROP last_name');
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'ALTER TABLE users CHANGE realname first_name VARCHAR(150) NULL DEFAULT NULL');
		$db->query(NULL, 'ALTER TABLE users ADD last_name VARCHAR(150) NULL DEFAULT NULL AFTER first_name');

		// http://stackoverflow.com/questions/2696884/mysql-split-value-from-one-field-to-two
		$db->query(NULL, 'UPDATE users SET last_name = SUBSTRING_INDEX(SUBSTRING_INDEX(first_name, " ", 2), " ", -1)');
		$db->query(NULL, 'UPDATE users SET first_name = SUBSTRING_INDEX(SUBSTRING_INDEX(first_name, " ", 1), " ", -1)');
	}

}
