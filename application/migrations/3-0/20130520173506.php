<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Ushahidi Database Migration
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Migrations
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Migration_3_0_20130520173506 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Add unique keys to Table `sets`
		$db->query(NULL, "ALTER TABLE `sets`
		  ADD UNIQUE INDEX `unq_sets_name` (`name`),
		  MODIFY COLUMN `user_id` INT(11) UNSIGNED NULL DEFAULT NULL;");
		
		$db->query(NULL, "ALTER TABLE `sets`
		  ADD CONSTRAINT `fk_sets_user_id`
			FOREIGN KEY (`user_id`)
			REFERENCES `users` (`id`)
			ON DELETE SET NULL;
		");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		// Remove unique keys from Table `sets`
		$db->query(NULL, "ALTER TABLE `sets`
		  DROP INDEX `unq_sets_name`,
		  MODIFY COLUMN `user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
		  DROP FOREIGN KEY `fk_sets_user_id`;
		");
	}


}
