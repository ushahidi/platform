<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Ushahidi Database Migration
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Migrations
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Migration_3_0_20130430204508 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Table `form_groups_form_attributes`
		$db->query(NULL, "CREATE  TABLE IF NOT EXISTS `form_groups_form_attributes` (
		  `form_group_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
		  `form_attribute_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
		  PRIMARY KEY (`form_group_id`,`form_attribute_id`),
		  KEY `fk_form_groups_form_attributes_form_group_id` (`form_group_id`),
		  CONSTRAINT `fk_form_groups_form_attributes_form_group_id`
		    FOREIGN KEY (`form_group_id`)
		    REFERENCES `form_groups` (`id`)
		    ON DELETE CASCADE,
		  KEY `fk_form_groups_form_attributes_form_attribute_id` (`form_attribute_id`),
		  CONSTRAINT `fk_form_groups_form_attributes_form_attribute_id`
		    FOREIGN KEY (`form_attribute_id`)
		    REFERENCES `form_attributes` (`id`)
		    ON DELETE CASCADE )
		ENGINE = InnoDB
		DEFAULT CHARACTER SET = utf8;");
		
		// Populate form_groups_form_attributes table
		$db->query(Database::INSERT, "INSERT INTO `form_groups_form_attributes` (`form_group_id`, `form_attribute_id`)
		  SELECT `form_group_id`, `id` FROM form_attributes");

		// Drop form_id/form_group_id columns from `form_attributes`
		$db->query(NULL, "ALTER TABLE `form_attributes` 
		  DROP COLUMN form_id,
		  DROP COLUMN form_group_id,
		  DROP FOREIGN KEY fk_form_attributes_form_id,
		  DROP FOREIGN KEY fk_form_attributes_form_group_id,
		  DROP INDEX idx_form_group_id,
		  DROP INDEX unq_form_id_key,
		  ADD UNIQUE KEY `unq_key` (`key` ASC)
		");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "SET FOREIGN_KEY_CHECKS=0;");
		$db->query(NULL, 'DROP TABLE `form_groups_form_attributes`;');

		// Drop form_id/form_group_id columns from `form_attributes`
		$db->query(NULL, "ALTER TABLE `form_attributes` 
		  DROP KEY `unq_key`,
		  ADD COLUMN `form_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
		  ADD COLUMN `form_group_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
		  ADD UNIQUE INDEX `unq_form_id_key` (`form_id` ASC, `key` ASC) ,
		  ADD INDEX `idx_form_group_id` (`form_group_id` ASC),
		  ADD INDEX `fk_form_attributes_form_id` (`form_id` ASC),
		  ADD INDEX `fk_form_attributes_form_group_id` (`form_group_id` ASC),
		  ADD CONSTRAINT `fk_form_attributes_form_id`
		    FOREIGN KEY (`form_id`)
		    REFERENCES `forms` (`id`)
		    ON DELETE CASCADE ,
		  ADD CONSTRAINT `fk_form_attributes_form_group_id`
		    FOREIGN KEY (`form_group_id`)
		    REFERENCES `form_groups` (`id`)
		    ON DELETE CASCADE
		");

		$db->query(NULL, "SET FOREIGN_KEY_CHECKS=1;");
	}

}
