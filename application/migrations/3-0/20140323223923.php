<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20140323223923 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL,
			'CREATE TABLE `posts_media` (
				 `post_id` int(11) unsigned NOT NULL,
				 `media_id` int(11) unsigned NOT NULL,
				 UNIQUE KEY `uniq_post_media` (`post_id`,`media_id`),
				 KEY `fk_media_id` (`media_id`))');
		$db->query(NULL,
			'ALTER TABLE `posts_media`
				  ADD CONSTRAINT `fk_media_id` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`)
					  ON DELETE CASCADE ON UPDATE CASCADE,
				  ADD CONSTRAINT `fk_posts_id` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
					  ON DELETE CASCADE ON UPDATE CASCADE');
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'DROP TABLE `posts_media`');
	}

}
