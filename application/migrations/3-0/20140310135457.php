<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20140310135457 extends Minion_Migration_Base {

	private $_oauth_tables = array(
		'oauth_access_tokens',
		'oauth_authorization_codes',
		'oauth_clients',
		'oauth_refresh_tokens',
		);

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Replace the "created" column with a unix timestamp
		foreach ($this->_oauth_tables as $table)
		{
			$db->query(NULL, "ALTER TABLE `{$table}` ADD `creatednew` INT(11) UNSIGNED NOT NULL");
			$db->query(NULL, "UPDATE `{$table}` SET creatednew = UNIX_TIMESTAMP(created)");
			$db->query(NULL, "ALTER TABLE `{$table}` DROP `created`");
			$db->query(NULL, "ALTER TABLE `{$table}` CHANGE `creatednew` `created` INT(11) UNSIGNED NOT NULL");
		}
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		// Replace the "created" column with a timestamp
		foreach ($this->_oauth_tables as $table)
		{
			$db->query(NULL, "ALTER TABLE `{$table}` ADD `creatednew` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
			$db->query(NULL, "UPDATE `{$table}` SET creatednew = FROM_UNIXTIME(created)");
			$db->query(NULL, "ALTER TABLE `{$table}` DROP `created`");
			$db->query(NULL, "ALTER TABLE `{$table}` CHANGE `creatednew` `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
		}
	}

}
