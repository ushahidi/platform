<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20130509175301 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Drop unique column from `form_attributes`
		$db->query(NULL, "ALTER TABLE `form_attributes` 
		  DROP COLUMN `unique`,
		  ADD COLUMN `cardinality` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1'
		    COMMENT 'Max number of values, 0 = Unlimited';");
		
		// Make title NOT NULL from `posts`
		$db->query(NULL, "ALTER TABLE `posts` 
		  MODIFY COLUMN `title` VARCHAR(150) NOT NULL DEFAULT '',
		  DROP COLUMN `author`,
		  DROP COLUMN `email`;");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `form_attributes` 
		  ADD COLUMN `unique` TINYINT(1) NOT NULL DEFAULT '0',
		  DROP COLUMN `cardinality`;");
		
		$db->query(NULL, "ALTER TABLE `posts` 
		  MODIFY COLUMN `title` VARCHAR(150) NULL DEFAULT NULL,
		  ADD COLUMN `author` VARCHAR(150) DEFAULT NULL,
		  ADD COLUMN `email` VARCHAR(150) DEFAULT NULL;");
	}

}
