<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20131105024214 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Add demo user with password 'testing' and role 'admin'
		DB::query(DATABASE::INSERT, "
			INSERT INTO `users` (`username`, `password`, `logins`, `failed_attempts`, `last_login`, `last_attempt`, `created`, `updated`, `role`)
			VALUES
				('demo', '$2y$15$5AHQqCP3.l9VLmsfErX/o.pAlbPUwODNeInZWeMpj44R0vvup/xUG', 0, 0, NULL, NULL, 0, 0, 'admin');
			")->execute($db);
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		// $db->query(NULL, 'DROP TABLE ... ');
	}

}
