<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_3_0_20130604220936 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `users`
			ADD COLUMN `failed_attempts` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `logins`,
			ADD COLUMN `last_attempt` INT(10) UNSIGNED DEFAULT NULL AFTER `last_login`,
			MODIFY COLUMN `email` VARCHAR(127) NULL DEFAULT NULL
		");
		
		$db->query(NULL, "DROP TABLE `roles_users`");
		
		$db->query(NULL, "DROP TABLE `roles`");
		
		// Table `roles`
		$db->query(NULL, "CREATE  TABLE IF NOT EXISTS `roles` (
		  `name` VARCHAR(32) NOT NULL ,
		  `display_name` VARCHAR(32) NOT NULL ,
		  `description` VARCHAR(255) NULL DEFAULT NULL ,
		  `permissions` VARCHAR(255) NULL DEFAULT NULL ,
		  PRIMARY KEY (`name`) ,
		  UNIQUE INDEX `display_name` (`display_name` ASC) )
		ENGINE = InnoDB
		DEFAULT CHARACTER SET = utf8;");
		
		$db->query(NULL, "INSERT INTO `roles`
			(`name`, `display_name`, `description`)
			VALUES
			('guest', 'Guest', 'Role given to users who are not logged in'),
			('user', 'User', 'Default logged in user role'),
			('admin', 'Admin', 'Administrator')
			");
		
		$db->query(NULL, "ALTER TABLE `users`
		  ADD COLUMN `role` VARCHAR(127) NULL DEFAULT 'user',
		  ADD CONSTRAINT `fk_users_role`
		    FOREIGN KEY (`role` )
		    REFERENCES `roles` (`name`)
		    ON DELETE SET NULL
		");
		
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `users`
			DROP COLUMN `failed_attempts`,
			DROP COLUMN `last_attempt`,
			MODIFY COLUMN `email` VARCHAR(127) NOT NULL,
			DROP COLUMN `role`,
			DROP FOREIGN KEY `fk_users_role`
		");
		
		$db->query(NULL, "DROP TABLE `roles`");

		// Table `roles`
		$db->query(NULL, "CREATE  TABLE IF NOT EXISTS `roles` (
		  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
		  `name` VARCHAR(32) NOT NULL ,
		  `description` VARCHAR(255) NULL DEFAULT NULL ,
		  `permissions` VARCHAR(255) NULL DEFAULT NULL ,
		  `user_id` INT(11) NULL DEFAULT NULL ,
		  PRIMARY KEY (`id`) ,
		  UNIQUE INDEX `name` (`name` ASC) )
		ENGINE = InnoDB
		DEFAULT CHARACTER SET = utf8;");

		// Table `roles_users`
		$db->query(NULL, "CREATE  TABLE IF NOT EXISTS `roles_users` (
		  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
		  `role_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
		  PRIMARY KEY (`user_id`, `role_id`) ,
		  INDEX `fk_roles_users_role_id` (`role_id` ASC) ,
		  CONSTRAINT `fk_roles_users_role_id`
		    FOREIGN KEY (`role_id` )
		    REFERENCES `roles` (`id` )
		    ON DELETE CASCADE,
		  CONSTRAINT `fk_roles_users_user_id`
		    FOREIGN KEY (`user_id` )
		    REFERENCES `users` (`id` )
		    ON DELETE CASCADE )
		ENGINE = InnoDB
		DEFAULT CHARACTER SET = utf8;");
	}

}
