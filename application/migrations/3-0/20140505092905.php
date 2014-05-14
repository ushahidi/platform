<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20140505092905 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Remove data_feed_id column and foreign key
		$db->query(NULL, "ALTER TABLE `messages`
			DROP COLUMN `data_feed_id`,
			DROP FOREIGN KEY `fk_messages_data_feeds_data_feed_id`
		");

		// Drop the table
		$db->query(NULL, 'DROP TABLE  `data_feeds` ');
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		// Recreate data feeds table
		$db->query(NULL, "CREATE TABLE IF NOT EXISTS `data_feeds` (
			`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
			`data_provider` VARCHAR(150) NOT NULL DEFAULT '' ,
			`name` VARCHAR(150) NOT NULL DEFAULT '' ,
			`options` TEXT NULL DEFAULT NULL COMMENT 'JSON options object' ,
			`created` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
			PRIMARY KEY (`id`),
			KEY `idx_data_provider` (`data_provider`)
		)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET = utf8;");

		// Re-add data_feed_id column to messages table
		$db->query(NULL, "ALTER TABLE `messages` ADD COLUMN data_feed_id INT(11) UNSIGNED NULL DEFAULT NULL");

		// Re-add foreign key constraints
		$db->query(NULL, "ALTER TABLE `messages` ADD CONSTRAINT `fk_messages_data_feeds_data_feed_id` FOREIGN KEY (data_feed_id) REFERENCES `data_feeds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
	}

}
