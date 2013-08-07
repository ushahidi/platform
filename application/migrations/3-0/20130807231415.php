<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20130807231415 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		DB::query(Database::INSERT, "
			INSERT IGNORE INTO `oauth_clients` (`client_id`, `client_secret`, `redirect_uri`)
			VALUES
			('ushahidiui', '', :base_url)")
			->param(':base_url', Kohana::$base_url)
			->execute($db);
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "DELETE IGNORE FROM `oauth_clients` WHERE `client_id` = 'ushahidiui'");
	}

}
