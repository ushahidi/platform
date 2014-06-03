<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_Demo_data_20140416012649 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, 'SET FOREIGN_KEY_CHECKS = 0;');

		$db->query(NULL, 'TRUNCATE `config`');

		$db->query(NULL, "INSERT INTO `config` (`id`, `group_name`, `config_key`, `config_value`, `updated`)
			VALUES
				(1,'site','site_name','\\\"testing site\\\"','2014-04-15 11:38:35'),
				(2,'site','owner_name','\\\"testerbot\\\"','2014-04-15 11:38:35'),
				(3,'test','testkey','\\\"testval\\\"','2014-04-15 11:38:35')
			");

		$db->query(NULL, "TRUNCATE `contacts`");

		$db->query(NULL, "INSERT INTO `contacts` (`id`, `user_id`, `data_provider`, `type`, `contact`, `created`)
			VALUES
				(1,1,'','phone','123456789',0),
				(2,NULL,'','email','somejunkemail@v3.ushahidi.com',0),
				(3,NULL,'','phone','773456789',0);
			");

		$db->query(NULL, "TRUNCATE `data_feeds`");

		$db->query(NULL, "INSERT INTO `data_feeds` (`id`, `data_provider`, `name`, `options`, `created`)
		VALUES
			(1,'smssync','SMSSync SMS Messages','{}',0);
		");

		$db->query(NULL, "TRUNCATE `form_attributes`");

		$db->query(NULL, "INSERT INTO `form_attributes` (`id`, `key`, `label`, `input`, `type`, `required`, `default`, `priority`, `options`, `cardinality`)
		VALUES
			(1,'test_varchar','Test varchar','text','varchar',0,NULL,1,'',1),
			(2,'test_point','Test point','location','point',0,NULL,1,'',1),
			(3,'full_name','Full Name','text','varchar',0,NULL,1,'',1),
			(4,'description','Description','textarea','text',0,NULL,2,'',1),
			(5,'date_of_birth','Date of birth','date','datetime',0,NULL,3,'',1),
			(6,'missing_date','Missing date','date','datetime',0,NULL,4,'',1),
			(7,'last_location','Last Location','text','varchar',1,NULL,5,'',1),
			(8,'last_location_point','Last Location (point)','location','point',0,NULL,5,'',0),
			(9,'geometry_test','Geometry test','text','geometry',0,NULL,5,'',1),
			(10,'missing_status','Status','select','varchar',0,NULL,5,'[\\\"information_sought\\\",\\\"is_note_author\\\",\\\"believed_alive\\\",\\\"believed_missing\\\",\\\"believed_dead\\\"]',0),
			(11,'links','Links','text','link',0,NULL,7,NULL,0),
			(12,'second_point','Second Point','location','point',0,NULL,5,'',1),
			(13,'location','Location','location','point',0,NULL,2,'',1);
		");

		$db->query(NULL, "TRUNCATE `form_groups`");

		$db->query(NULL, "INSERT INTO `form_groups` (`id`, `form_id`, `label`, `priority`, `icon`)
		VALUES
			(1,1,'Main',99,''),
			(2,2,'Main',99,''),
			(3,3,'Main',99,'');
		");

		$db->query(NULL, "TRUNCATE `form_groups_form_attributes`");

		$db->query(NULL, "INSERT INTO `form_groups_form_attributes` (`form_group_id`, `form_attribute_id`)
		VALUES
			(1,1),
			(1,2),
			(1,3),
			(1,4),
			(1,5),
			(1,6),
			(1,7),
			(1,8),
			(1,9),
			(1,10),
			(1,11),
			(1,12),
			(2,13),
			(3,10);
		");

		$db->query(NULL, "TRUNCATE `forms`");

		$db->query(NULL, "INSERT INTO `forms` (`id`, `parent_id`, `name`, `description`, `type`, `created`, `updated`)
		VALUES
			(1,0,'Test Form','Testing form','report',0,0),
			(2,0,'Post form','Post form','report',0,0),
			(3,0,'Missing people','Missing persons','report',0,0);
		");

		$db->query(NULL, "TRUNCATE `media`");

		$db->query(NULL, "INSERT INTO `media` (`id`, `mime`, `caption`, `o_filename`, `o_width`, `o_height`, `created`, `updated`)
		VALUES
			(1,'image/jpeg','ihub','9ze_1381815819_o.jpg',400,500,1381815821,1381815819),
			(2,'image/jpeg','at sendai','9ze_1381815819_o.jpg',500,600,1381815821,1381815819),
			(3,'image/jpeg','ihub','9ze_1381815819_o.jpg',600,700,1381815821,1381815819);
		");

		$db->query(NULL, "TRUNCATE `messages`");

		$db->query(NULL, "INSERT INTO `messages` (`id`, `parent_id`, `contact_id`, `post_id`, `data_feed_id`, `data_provider`, `data_provider_message_id`, `title`, `message`, `datetime`, `type`, `status`, `direction`, `created`)
		VALUES
			(1,NULL,1,NULL,NULL,NULL,NULL,'abc','A test message',NULL,'sms','received','incoming',0),
			(2,NULL,3,NULL,NULL,NULL,NULL,'','Another test message',NULL,'sms','received','incoming',0),
			(3,NULL,2,NULL,NULL,NULL,NULL,'Test email','test email body abc','2013-01-02 07:07:00','email','received','incoming',0),
			(4,NULL,3,110,1,NULL,NULL,'','Another message with a post',NULL,'sms','received','incoming',0),
			(5,NULL,1,NULL,NULL,'smssync',NULL,'','A test message with provider',NULL,'sms','received','incoming',0),
			(6,NULL,3,NULL,1,NULL,NULL,'','Archived message',NULL,'sms','archived','incoming',0),
			(7,NULL,3,NULL,NULL,NULL,NULL,'','Pending outgoing message',NULL,'sms','pending','outgoing',0),
			(8,NULL,3,NULL,NULL,NULL,NULL,'','Outgoing message',NULL,'sms','sent','outgoing',0);
		");

		$db->query(NULL, "TRUNCATE `post_comments`");
		$db->query(NULL, "TRUNCATE `post_datetime`");
		$db->query(NULL, "TRUNCATE `post_decimal`");

		$db->query(NULL, "TRUNCATE `post_geometry`");
		$db->query(NULL, "INSERT INTO `post_geometry` (`id`, `post_id`, `form_attribute_id`, `value`, `created`)
		VALUES
			(1,1,9, GeomFromText('MULTIPOLYGON (((40 40, 20 45, 45 30, 40 40)),
					((20 35, 45 20, 30 5, 10 10, 10 30, 20 35),
					(30 20, 20 25, 20 15, 30 20)))'),0);
		");
		$db->query(NULL, "TRUNCATE `post_int`");
		$db->query(NULL, "TRUNCATE `post_point`");

		$db->query(NULL, "INSERT INTO `post_point` (`id`, `post_id`, `form_attribute_id`, `value`, `created`)
		VALUES
			(1,1,8, POINT(12.123, 21.213),0),
			(2,99,8, POINT(11.123, 24.213),0),
			(3,9999,8, POINT(10.123, 26.213),0),
			(4,95,8, POINT(1, 1),0),
			(5,95,12, POINT(1.2, 0.5),0),
			(6,97,8, POINT(1, 1),0),
			(7,1,8, POINT(12.223, 21.313),0);
		");

		$db->query(NULL, "TRUNCATE `post_text`");

		$db->query(NULL, "TRUNCATE `post_varchar`");

		$db->query(NULL, "INSERT INTO `post_varchar` (`id`, `post_id`, `form_attribute_id`, `value`, `created`)
		VALUES
			(1,1,10,'believed_missing',0),
			(2,101,1,'dummy string',0),
			(3,102,10,'believed_missing',0),
			(4,99,1,'some-string',0),
			(5,103,1,'previous_string',0),
			(6,104,1,'some-string',0),
			(7,105,1,'some-string',0),
			(8,106,1,'french string',0),
			(9,107,1,'french string',0),
			(10,108,1,'arabic string',0),
			(11,1,11,'http://google.com',0),
			(12,1,11,'http://ushahidi.com',0),
			(50,97,1,'special-string',0);
		");

		$db->query(NULL, "TRUNCATE `posts`");

		$db->query(NULL, "INSERT INTO `posts` (`id`, `parent_id`, `form_id`, `user_id`, `type`, `title`, `slug`, `content`, `status`, `created`, `updated`, `locale`)
		VALUES
			(1,NULL,1,NULL,'report','Test post',NULL,'Testing post','published',0,0,'en_us'),
			(95,NULL,1,NULL,'report','OAuth test post',NULL,'Testing oauth posts api access','published',0,0,'en_us'),
			(96,NULL,1,NULL,'report','French post to test Searching',NULL,'Some description','published',0,0,'fr_fr'),
			(97,NULL,1,NULL,'report','search by attribute',NULL,'Some description','published',0,0,'en_us'),
			(99,NULL,1,NULL,'report','Should be returned when Searching',NULL,'Some description','published',0,0,'en_us'),
			(101,99,1,NULL,'report','Child dummy report',NULL,'Some description','published',0,0,'en_us'),
			(102,99,3,NULL,'report','Child missing person report',NULL,'Some description','published',0,0,'en_us'),
			(103,99,1,NULL,'revision','Should be returned when Searching',NULL,'Some description','published',0,0,'en_us'),
			(104,99,1,NULL,'revision','Should be returned when Searching',NULL,'Some description','published',0,0,'en_us'),
			(105,NULL,1,NULL,'report','Original post',NULL,'Some description','published',0,0,'en_us'),
			(106,105,1,NULL,'translation','French post',NULL,'Some description','published',0,0,'fr_fr'),
			(107,106,1,NULL,'revision','French post',NULL,'Some description','published',0,0,'fr_fr'),
			(108,105,1,NULL,'translation','Arabic post',NULL,'Some description','published',0,0,'ar_ar'),
			(110,NULL,1,1,'report','ACL test post',NULL,'Testing oauth posts api access','published',0,0,'en_us'),
			(111,NULL,1,1,'report','ACL private post',NULL,'Testing oauth posts api access','draft',0,0,'en_us'),
			(112,NULL,1,NULL,'report','Draft 1',NULL,'Testing draft','draft',0,0,'en_us'),
			(113,NULL,1,NULL,'report','Draft 2',NULL,'Testing draft 2','pending',0,0,'en_us'),
			(114,111,1,NULL,'report','Update for draft',NULL,'Update for draft','published',0,0,'en_us'),
			(115,111,1,NULL,'translation','Translation of draft',NULL,'Translation of draft','published',0,0,'fr_fr'),
			(116,111,1,NULL,'revision','Revision of draft',NULL,'Revision of draft','published',0,0,'en_us'),
			(117,110,1,NULL,'report','Update for draft',NULL,'Update for draft','draft',0,0,'en_us'),
			(9999,NULL,1,NULL,'report','another report',NULL,'Some description','published',0,0,'en_us');
		");

		$db->query(NULL, "TRUNCATE `posts_sets`");

		$db->query(NULL, "INSERT INTO `posts_sets` (`post_id`, `set_id`)
		VALUES
			(1,1),
			(111,1),
			(112,1),
			(113,1),
			(114,1),
			(9999,1);
		");

		$db->query(NULL, "TRUNCATE `posts_tags`");

		$db->query(NULL, "INSERT INTO `posts_tags` (`post_id`, `tag_id`)
		VALUES
			(1,3),
			(99,3),
			(1,4);
		");

		$db->query(NULL, "TRUNCATE `roles`");

		$db->query(NULL, "INSERT INTO `roles` (`name`, `display_name`, `description`, `permissions`)
		VALUES
			('admin','Admin','Administrator',NULL),
			('guest','Guest','Role given to users who are not logged in',NULL),
			('user','User','Default logged in user role',NULL);
		");

		$db->query(NULL, "TRUNCATE `sets`");

		$db->query(NULL, "INSERT INTO `sets` (`id`, `user_id`, `name`, `filter`, `created`, `updated`)
		VALUES
			(1,NULL,'Test set',NULL,0,0),
			(2,NULL,'Explosion',NULL,0,0);
		");

		$db->query(NULL, "TRUNCATE `tags`");

		$db->query(NULL, "INSERT INTO `tags` (`id`, `parent_id`, `tag`, `slug`, `type`, `color`, `description`, `priority`, `created`)
		VALUES
			(1,0,'Test tag','test-tag','category',NULL,NULL,0,0),
			(2,0,'Duplicate','duplicate','category',NULL,NULL,0,0),
			(3,0,'Disaster','disaster','category',NULL,NULL,0,0),
			(4,3,'Explosion','explosion','category',NULL,NULL,0,0),
			(5,0,'Todo','todo','status',NULL,NULL,0,0),
			(6,0,'Done','done','status',NULL,NULL,0,0);
		");

		$db->query(NULL, "TRUNCATE `tasks`");

		$db->query(NULL, "TRUNCATE `users`");

		$db->query(NULL, 'INSERT INTO `users` (`id`, `email`, `first_name`, `last_name`, `username`, `password`, `logins`, `failed_attempts`, `last_login`, `last_attempt`, `created`, `updated`, `role`)
		VALUES
			(1,\'robbie@ushahidi.com\',\'Robbie\',\'Mackay\',\'robbie\',\'$2y$15$iWANGZn.DomLWU.YtjUcX.HEq1hoMGauzXFRubKgar/BRaAj9zQ9q\',0,0,NULL,NULL,0,0,\'user\'),
			(2,NULL,NULL,NULL,\'admin\',\'$2y$15$iWANGZn.DomLWU.YtjUcX.HEq1hoMGauzXFRubKgar/BRaAj9zQ9q\',0,0,NULL,NULL,0,0,\'admin\'),
			(3,\'test@v3.ushahidi.com\',\'Test\',\'User\',\'test\',\'$2y$15$iWANGZn.DomLWU.YtjUcX.HEq1hoMGauzXFRubKgar/BRaAj9zQ9q\',0,0,NULL,NULL,0,0,\'user\'),
			(4,NULL,NULL,NULL,\'importadmin\',\'$2y$15$iWANGZn.DomLWU.YtjUcX.HEq1hoMGauzXFRubKgar/BRaAj9zQ9q\',0,0,NULL,NULL,0,0,\'admin\'),
			(5,NULL,NULL,NULL,\'demo\',\'$2y$15$iWANGZn.DomLWU.YtjUcX.HEq1hoMGauzXFRubKgar/BRaAj9zQ9q\',0,0,NULL,NULL,0,0,\'admin\');
		');

		$db->query(NULL, 'SET FOREIGN_KEY_CHECKS = 1;');
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'SET FOREIGN_KEY_CHECKS = 0;');
		$db->query(NULL, 'TRUNCATE `config`');
		$db->query(NULL, "TRUNCATE `contacts`");
		$db->query(NULL, "TRUNCATE `data_feeds`");
		$db->query(NULL, "TRUNCATE `form_attributes`");
		$db->query(NULL, "TRUNCATE `form_groups`");
		$db->query(NULL, "TRUNCATE `form_groups_form_attributes`");
		$db->query(NULL, "TRUNCATE `forms`");
		$db->query(NULL, "TRUNCATE `media`");
		$db->query(NULL, "TRUNCATE `messages`");
		$db->query(NULL, "TRUNCATE `post_comments`");
		$db->query(NULL, "TRUNCATE `post_datetime`");
		$db->query(NULL, "TRUNCATE `post_decimal`");
		$db->query(NULL, "TRUNCATE `post_geometry`");
		$db->query(NULL, "TRUNCATE `post_int`");
		$db->query(NULL, "TRUNCATE `post_point`");
		$db->query(NULL, "TRUNCATE `post_text`");
		$db->query(NULL, "TRUNCATE `post_varchar`");
		$db->query(NULL, "TRUNCATE `posts`");
		$db->query(NULL, "TRUNCATE `posts_sets`");
		$db->query(NULL, "TRUNCATE `posts_tags`");
		$db->query(NULL, "TRUNCATE `roles`");
		$db->query(NULL, "TRUNCATE `sets`");
		$db->query(NULL, "TRUNCATE `tags`");
		$db->query(NULL, "TRUNCATE `tasks`");
		$db->query(NULL, "TRUNCATE `users`");
		$db->query(NULL, 'SET FOREIGN_KEY_CHECKS = 1;');
	}

}
