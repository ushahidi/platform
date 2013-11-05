<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20131028221729 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		list($form_id, $affected_rows) = DB::query(DATABASE::INSERT, "
			INSERT INTO `forms` (`parent_id`, `name`, `description`, `type`, `created`, `updated`)
			VALUES
				(0,'Post','Post form','report',0,0);
			")->execute($db);

		list($group_id, $affected_rows) = DB::query(DATABASE::INSERT, "
			INSERT INTO `form_groups` (`form_id`, `label`, `priority`)
			VALUES
				(:form_id,'Main',99);
			")->bind(':form_id', $form_id)
			->execute($db);

		// Check for 'location' field and create if it doesn't exist
		$field_id = DB::query(DATABASE::SELECT, "
			SELECT id FROM `form_attributes` WHERE `key` = 'location';
			")->execute($db)->get('id', FALSE);
		if (! $field_id)
		{
			list($field_id, $affected_rows) = DB::query(DATABASE::INSERT, "
				INSERT INTO `form_attributes` (`key`, `label`, `input`, `type`, `required`, `default`, `priority`, `options`, `cardinality`)
				VALUES
					('location','Location','location','point',0,NULL,2,'',1)
				")->execute($db);
		}

		DB::query(DATABASE::INSERT, "
			INSERT INTO `form_groups_form_attributes` (`form_group_id`, `form_attribute_id`)
			VALUES
				(:group,:field)
			")->bind(':group', $group_id)
			->bind(':field', $field_id)
			->execute($db);

		list($post_id, $affected_rows) = DB::query(DATABASE::INSERT, "
			INSERT INTO `posts` (`form_id`, `type`, `title`, `content`, `status`)
			VALUES
				(:form_id, 'report', 'First post', 'Delete this post and add your own!', 'published')
			")->bind(':form_id', $form_id)
			->execute($db);

		DB::query(DATABASE::INSERT, "
			INSERT INTO `post_point` (`post_id`, `form_attribute_id`, `value`)
			VALUES
				(:post, :field_id, GeomFromText('POINT(174.78 -36.85)'))
			")->bind(':field_id', $field_id)
			->bind(':post', $post_id)
			->execute($db);

		DB::query(DATABASE::INSERT, "
			INSERT INTO `tags` (`tag`, `slug`, `type`, `color`, `description`, `priority`)
			VALUES
				('example1', 'example1', 'category', 'ff0000', 'Example Category', 0),
				('example2', 'example2', 'category', '00ff00', 'Example Category', 0),
				('example3', 'example3', 'category', '0000ff', 'Example Category', 0)
			")->execute($db);
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		// $db->query(NULL, 'DROP TABLE ... ');
	}

}
