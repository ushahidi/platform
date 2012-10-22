CREATE TABLE `alerts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `type` varchar(30) NOT NULL DEFAULT 'email' COMMENT 'email, sms',
  `recipient` varchar(100) NOT NULL DEFAULT '',
  `code` varchar(50) NOT NULL DEFAULT '',
  `status` varchar(30) NOT NULL DEFAULT 'pending' COMMENT 'pending, confirmed',
  `point` point DEFAULT NULL,
  `radius` tinyint(4) NOT NULL DEFAULT '100',
  `ip_address` int(11) DEFAULT '0',
  `created` int(10) NOT NULL DEFAULT '0',
  `updated` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_status` (`status`),
  KEY `fk_alerts_user_id` (`user_id`),
  CONSTRAINT `fk_alerts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `alerts_posts` (
  `alert_id` int(11) unsigned NOT NULL DEFAULT '0',
  `post_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`alert_id`,`post_id`),
  KEY `fk_alerts_posts_post_id` (`post_id`),
  CONSTRAINT `fk_alerts_posts_alert_id` FOREIGN KEY (`alert_id`) REFERENCES `alerts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_alerts_posts_post_id` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `alerts_tags` (
  `alert_id` int(11) unsigned NOT NULL DEFAULT '0',
  `tag_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`alert_id`,`tag_id`),
  KEY `fk_alerts_tags_tag_id` (`tag_id`),
  CONSTRAINT `fk_alerts_tags_alert_id` FOREIGN KEY (`alert_id`) REFERENCES `alerts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_alerts_tags_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;