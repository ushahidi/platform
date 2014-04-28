<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20140317193349 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// update media table, add missing author association
		$db->query(NULL, 'ALTER TABLE `media` ADD `user_id` INT(11) UNSIGNED DEFAULT NULL COMMENT "author" AFTER `id`;');
		$db->query(NULL, 'CREATE INDEX `fk_media_user_id` ON `media` (`user_id` ASC);');
		$db->query(NULL, 'ALTER TABLE `media` ADD CONSTRAINT `fk_media_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;');
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'ALTER TABLE `media` DROP FOREIGN KEY `fk_media_user_id`;');
		$db->query(NULL, 'ALTER TABLE `media` DROP COLUMN `user_id`;');
	}

}
