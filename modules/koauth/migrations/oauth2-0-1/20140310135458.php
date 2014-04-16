<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_Oauth2_0_1_20140310135458 extends Minion_Migration_Base {

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
			// The minimum value for TIMESTAMP is 1970-01-01 00:00:01 so we have to handle create = 0 differently
			$db->query(NULL, "UPDATE `{$table}` SET creatednew = FROM_UNIXTIME(created) WHERE created <> 0");
			$db->query(NULL, "UPDATE `{$table}` SET creatednew = '0000-00-00 00:00:00' WHERE created = 0");
			$db->query(NULL, "ALTER TABLE `{$table}` DROP `created`");
			$db->query(NULL, "ALTER TABLE `{$table}` CHANGE `creatednew` `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
		}
	}

}
