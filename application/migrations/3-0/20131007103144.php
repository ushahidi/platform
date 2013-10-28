<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Ushahidi Database Migration
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Migrations
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Migration_3_0_20131007103144 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Make Sure We're Using UTF8 Encoding
		$db->query(NULL, 'ALTER DATABASE DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci');

		// Table `media`
		$db->query(NULL," CREATE  TABLE IF NOT EXISTS `media` (
		  `id` Int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `mime` VARCHAR(50) NOT NULL,
		  `caption` VARCHAR(255) NOT NULL DEFAULT '' ,
		  `o_filename` VARCHAR(100) NOT NULL,
		  `o_width` INT(11) DEFAULT NULL,
		  `o_height` INT(11) DEFAULT NULL,
		  `created` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
		  `updated` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
		  PRIMARY KEY (`id`) )
		ENGINE=InnoDB
		DEFAULT CHARSET=utf8;");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'DROP TABLE `media`;');
	}

}