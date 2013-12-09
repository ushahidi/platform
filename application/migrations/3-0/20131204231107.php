<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20131204231107 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "
			CREATE TABLE IF NOT EXISTS `config` (
				`id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
				`group_name` VARCHAR(255) NOT NULL DEFAULT '',
				`config_key` VARCHAR(255) NOT NULL DEFAULT '',
				`config_value` VARCHAR(255) NOT NULL DEFAULT '',
				`updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
				KEY `idx_group_name` (`group_name`),
				KEY `idx_config_key_group_name` (`group_name`, `config_key`),
				UNIQUE KEY `unq_config_key_group_name` (`group_name`,`config_key`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'DROP TABLE `config`');
	}

}
