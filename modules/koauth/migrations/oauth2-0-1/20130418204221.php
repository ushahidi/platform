<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Initial schema setup for oauth module
 */

class Migration_Oauth2_0_1_20130418204221 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, '
			CREATE TABLE IF NOT EXISTS `oauth_clients` (
				`client_id` VARCHAR(255) NOT NULL,
				`client_secret` VARCHAR(255) NOT NULL,
				`redirect_uri` VARCHAR(255) NOT NULL,
				`grant_types` VARCHAR(255) NOT NULL,
				`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`client_id`)
			)
			ENGINE = InnoDB
			DEFAULT CHARACTER SET = utf8;');
			
		$db->query(NULL, '
			CREATE TABLE IF NOT EXISTS `oauth_access_tokens` (
				`access_token` VARCHAR(255) NOT NULL,
				`client_id` VARCHAR(255) NOT NULL,
				`user_id` INT(11) UNSIGNED DEFAULT NULL,
				`expires` TIMESTAMP NOT NULL DEFAULT 0,
				`scope` VARCHAR(255) NOT NULL,
				created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`access_token`) ,
				INDEX `fk_oauth_access_tokens_client_id` (`client_id` ASC),
				CONSTRAINT `fk_oauth_access_tokens_client_id`
					FOREIGN KEY (`client_id`)
					REFERENCES `oauth_clients` (`client_id`)
					ON DELETE CASCADE ,
				INDEX `fk_oauth_access_tokens_user_id` (`user_id` ASC),
				CONSTRAINT `fk_oauth_access_tokens_user_id`
					FOREIGN KEY (`user_id`)
					REFERENCES `users` (`id`)
					ON DELETE CASCADE
			)
			ENGINE = InnoDB
			DEFAULT CHARACTER SET = utf8;');
		
		$db->query(NULL, '
			CREATE TABLE IF NOT EXISTS `oauth_authorization_codes` (
				`authorization_code` VARCHAR(255) NOT NULL,
				`client_id` VARCHAR(255) NOT NULL,
				`user_id` INT(11) UNSIGNED DEFAULT NULL,
				`redirect_uri` VARCHAR(255) NOT NULL,
				`expires` TIMESTAMP NOT NULL DEFAULT 0,
				`scope` VARCHAR(255) NOT NULL,
				`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`authorization_code`) ,
				INDEX `fk_oauth_authorization_codes_client_id` (`client_id` ASC),
				CONSTRAINT `fk_oauth_authorization_codes_client_id`
					FOREIGN KEY (`client_id`)
					REFERENCES `oauth_clients` (`client_id`)
					ON DELETE CASCADE ,
				INDEX `fk_oauth_authorization_codes_user_id` (`user_id` ASC),
				CONSTRAINT `fk_oauth_authorization_codes_user_id`
					FOREIGN KEY (`user_id`)
					REFERENCES `users` (`id`)
					ON DELETE CASCADE
			)
			ENGINE = InnoDB
			DEFAULT CHARACTER SET = utf8;');
		
		$db->query(NULL, '
			CREATE TABLE IF NOT EXISTS `oauth_refresh_tokens` (
				`refresh_token` VARCHAR(255) NOT NULL,
				`client_id` VARCHAR(255) NOT NULL,
				`user_id` INT(11) UNSIGNED DEFAULT NULL,
				`expires` TIMESTAMP NOT NULL DEFAULT 0,
				`scope` VARCHAR(255) NOT NULL,
				`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`refresh_token`) ,
				INDEX `fk_oauth_refresh_tokens_client_id` (`client_id` ASC),
				CONSTRAINT `fk_oauth_refresh_tokens_client_id`
					FOREIGN KEY (`client_id`)
					REFERENCES `oauth_clients` (`client_id`)
					ON DELETE CASCADE ,
				INDEX `fk_oauth_refresh_tokens_user_id` (`user_id` ASC),
				CONSTRAINT `fk_oauth_refresh_tokens_user_id`
					FOREIGN KEY (`user_id`)
					REFERENCES `users` (`id`)
					ON DELETE CASCADE
			)
			ENGINE = InnoDB
			DEFAULT CHARACTER SET = utf8;');
		
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'DROP TABLE oauth_access_tokens;');
		$db->query(NULL, 'DROP TABLE oauth_authorization_codes;');
		$db->query(NULL, 'DROP TABLE oauth_refresh_tokens;');
		$db->query(NULL, 'DROP TABLE oauth_clients;');
	}

}
