<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Ushahidi Database Migration
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Migrations
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

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
		  ADD UNIQUE INDEX `unq_tags_slug` (`slug`),
		  ADD UNIQUE INDEX `unq_tags_tag_parent_type` (`tag`, `parent_id`, `type`),
		  ADD COLUMN `color` VARCHAR(20) DEFAULT NULL AFTER `type`,
		  ADD COLUMN `description` TEXT AFTER `color`,
		  MODIFY COLUMN `type` VARCHAR(20) NOT NULL DEFAULT 'category';
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
		  DROP INDEX `unq_tags_tag_parent_type`,
		  DROP COLUMN `color`,
		  DROP COLUMN `description`,
		  MODIFY COLUMN `type` VARCHAR(20) NULL DEFAULT NULL;
		");
	}

}
