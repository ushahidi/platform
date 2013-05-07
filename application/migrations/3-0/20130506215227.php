<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20130506215227 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		
		// Add unique keys to Table `tags`
		$db->query(NULL, "ALTER TABLE `tags`
		  ADD UNIQUE INDEX `unq_tags_slug` (`slug` ASC),
		  ADD UNIQUE INDEX `unq_tags_tag` (`tag` ASC);
		");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		// Remove unique keys from Table `tags`
		$db->query(NULL, "ALTER TABLE `tags`
		  DROP INDEX `unq_tags_slug`,
		  DROP INDEX `unq_tags_tag`;
		");
	}

}
