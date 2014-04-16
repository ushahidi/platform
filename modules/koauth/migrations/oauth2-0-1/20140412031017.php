<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_Oauth2_0_1_20140412031017 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `oauth_clients`
			MODIFY COLUMN `redirect_uri` VARCHAR(255) NOT NULL DEFAULT '/',
			MODIFY COLUMN `created` INT(11) unsigned NOT NULL DEFAULT 0,
			MODIFY COLUMN `grant_types` VARCHAR(255) NOT NULL DEFAULT ''");

		$db->query(Database::UPDATE, "UPDATE `oauth_clients` SET `redirect_uri` = '/' WHERE `redirect_uri` = ''");

		$db->query(NULL, "ALTER TABLE `oauth_authorization_codes`
			MODIFY COLUMN `redirect_uri` VARCHAR(255) NOT NULL DEFAULT '/',
			MODIFY COLUMN `created` INT(11) unsigned NOT NULL DEFAULT 0,
			MODIFY COLUMN `scope` VARCHAR(255) NOT NULL DEFAULT ''");

		$db->query(Database::UPDATE, "UPDATE `oauth_authorization_codes` SET `redirect_uri` = '/' WHERE `redirect_uri` = ''");

		$db->query(NULL, "ALTER TABLE `oauth_access_tokens`
			MODIFY COLUMN `created` INT(11) unsigned NOT NULL DEFAULT 0,
			MODIFY COLUMN `scope` VARCHAR(255) NOT NULL DEFAULT ''");

		$db->query(NULL, "ALTER TABLE `oauth_refresh_tokens`
			MODIFY COLUMN `created` INT(11) unsigned NOT NULL DEFAULT 0,
			MODIFY COLUMN `scope` VARCHAR(255) NOT NULL DEFAULT ''");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `oauth_clients`
			MODIFY COLUMN `redirect_uri` VARCHAR(255) NOT NULL,
			MODIFY COLUMN `created` INT(11) unsigned NOT NULL,
			MODIFY COLUMN `grant_types` VARCHAR(255) NOT NULL");

		$db->query(NULL, "ALTER TABLE `oauth_authorization_codes`
			MODIFY COLUMN `redirect_uri` VARCHAR(255) NOT NULL,
			MODIFY COLUMN `created` INT(11) unsigned NOT NULL,
			MODIFY COLUMN `scope` VARCHAR(255) NOT NULL");

		$db->query(NULL, "ALTER TABLE `oauth_access_tokens`
			MODIFY COLUMN `created` INT(11) unsigned NOT NULL,
			MODIFY COLUMN `scope` VARCHAR(255) NOT NULL");

		$db->query(NULL, "ALTER TABLE `oauth_refresh_tokens`
			MODIFY COLUMN `created` INT(11) unsigned NOT NULL");
	}

}
