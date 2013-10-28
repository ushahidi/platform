<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Ushahidi Database Migration
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Migrations
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Migration_3_0_20130410191926 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Add 'locale' to Table `posts`
		$db->query(NULL, "ALTER TABLE `posts`
		  ADD COLUMN `locale` VARCHAR(5) NOT NULL DEFAULT 'en_us',
		  DROP INDEX `unq_slug`,
		  DROP FOREIGN KEY `fk_posts_parent_id`;
		");
		
		$db->query(NULL, "ALTER TABLE `posts`
		  ADD CONSTRAINT `fk_posts_parent_id`
		    FOREIGN KEY (`parent_id`)
		    REFERENCES `posts` (`id`)
		    ON DELETE CASCADE ;
		");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		// Remove 'locale' to Table `posts`
		$db->query(NULL, "ALTER TABLE `posts`
		  DROP COLUMN `locale`,
		  ADD UNIQUE INDEX `unq_slug` (`slug` ASC),
		  DROP FOREIGN KEY `fk_posts_parent_id`;
		");
		
		$db->query(NULL, "ALTER TABLE `posts`
		  ADD CONSTRAINT `fk_posts_parent_id`
		    FOREIGN KEY (`parent_id`)
		    REFERENCES `posts` (`id`)
		    ON DELETE SET NULL ;
		");
	}

}
