<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20131127220443 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Data feeds are configuration of a particular data provider
		// ie. Twitter search for #food
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

		$db->query(NULL, "CREATE TABLE IF NOT EXISTS `contacts` (
			`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
			`user_id` INT(11) UNSIGNED NULL DEFAULT NULL ,
			`data_provider` VARCHAR(150) NOT NULL DEFAULT '',
			`type` varchar(20) DEFAULT NULL COMMENT 'email, phone, twitter',
			`contact` VARCHAR(255) NOT NULL DEFAULT '' ,
			`created` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
			PRIMARY KEY (`id`),
			KEY `idx_data_provider` (`data_provider`),
			KEY `idx_contact` (`contact`),
			KEY `fk_contacts_users_user_id` (`user_id`),
			CONSTRAINT `fk_contacts_users_user_id`
				FOREIGN KEY (`user_id`)
				REFERENCES `users` (`id`)
				ON DELETE SET NULL
		)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET = utf8;");

		$db->query(NULL, "CREATE TABLE IF NOT EXISTS `messages` (
			`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
			`parent_id` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'Used to mark message being replied to',
			`contact_id` INT(11) UNSIGNED NULL DEFAULT NULL ,
			`post_id` INT(11) UNSIGNED NULL DEFAULT NULL ,
			`data_feed_id` INT(11) UNSIGNED NULL DEFAULT NULL ,
			`data_provider` VARCHAR(150) NULL DEFAULT NULL ,
			`data_provider_message_id` VARCHAR(511) NULL DEFAULT NULL ,
			`title` VARCHAR(255) NULL DEFAULT NULL,
			`message` TEXT,
			`datetime` DATETIME NULL DEFAULT NULL,
			`type` varchar(20) DEFAULT NULL COMMENT 'email, phone, twitter',
			`status` VARCHAR(20) NOT NULL DEFAULT 'pending' COMMENT 'pending, received, expired, cancelled, failed, unknown, sent',
			`direction` VARCHAR(20) NOT NULL DEFAULT 'incoming' COMMENT 'incoming, outgoing',
			`created` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
			PRIMARY KEY (`id`) ,
			KEY `idx_direction` (`direction`),
			KEY `idx_status` (`status`),
			KEY `idx_data_provider` (`data_provider`),
			KEY `fk_messages_parent_id` (`parent_id`),
			CONSTRAINT `fk_messages_parent_id`
				FOREIGN KEY (`parent_id`)
				REFERENCES `messages` (`id`)
				ON DELETE SET NULL ,
			KEY `fk_messages_contacts_contact_id` (`contact_id`),
			CONSTRAINT `fk_messages_contacts_contact_id`
				FOREIGN KEY (`contact_id`)
				REFERENCES `contacts` (`id`)
				ON DELETE SET NULL ,
			KEY `fk_messages_posts_post_id` (`post_id`),
			CONSTRAINT `fk_messages_posts_post_id`
				FOREIGN KEY (`post_id`)
				REFERENCES `posts` (`id`)
				ON DELETE SET NULL ,
			KEY `fk_messages_data_feeds_data_feed_id` (`data_feed_id`),
			CONSTRAINT `fk_messages_data_feeds_data_feed_id`
				FOREIGN KEY (`data_feed_id`)
				REFERENCES `data_feeds` (`id`)
				ON DELETE SET NULL
		)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET = utf8;");

		// Make title column optional - allow for
		$db->query(NULL, "ALTER TABLE `posts`
			MODIFY COLUMN `title` VARCHAR(150) NULL DEFAULT NULL;
		");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'DROP TABLE `messages`');
		$db->query(NULL, 'DROP TABLE `contacts`');
		$db->query(NULL, 'DROP TABLE `data_feeds`');
		$db->query(NULL, "ALTER TABLE `posts`
			MODIFY COLUMN `title` VARCHAR(150) NOT NULL DEFAULT '';
		");
	}

}
