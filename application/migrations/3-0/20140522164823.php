<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20140522164823 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "UPDATE ushahidi.oauth_clients SET client_secret = SHA1('ushahidiui') WHERE oauth_clients.client_id = 'ushahidiui'");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "UPDATE ushahidi.oauth_clients SET client_secret = '' WHERE oauth_clients.client_id = 'ushahidiui'");
	}

}
