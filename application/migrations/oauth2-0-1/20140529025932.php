<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_Oauth2_0_1_20140529025932 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Replacing OAuth server, see D120
		$db->query(NULL, 'DROP TABLE IF EXISTS oauth_access_tokens;');
		$db->query(NULL, 'DROP TABLE IF EXISTS oauth_authorization_codes;');
		$db->query(NULL, 'DROP TABLE IF EXISTS oauth_refresh_tokens;');
		$db->query(NULL, 'DROP TABLE IF EXISTS oauth_clients;');
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		// Reinstate the old OAuth server
		$db->query(NULL, "
			CREATE TABLE oauth_access_tokens (
				access_token varchar(255) NOT NULL,
				client_id varchar(255) NOT NULL,
				user_id int(11) unsigned DEFAULT NULL,
				expires timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
				scope varchar(255) NOT NULL DEFAULT '',
				created int(11) unsigned NOT NULL DEFAULT '0'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		$db->query(NULL, "
			CREATE TABLE oauth_authorization_codes (
				authorization_code varchar(255) NOT NULL,
				client_id varchar(255) NOT NULL,
				user_id int(11) unsigned DEFAULT NULL,
				redirect_uri varchar(255) NOT NULL DEFAULT '/',
				expires timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
				scope varchar(255) NOT NULL DEFAULT '',
				created int(11) unsigned NOT NULL DEFAULT '0'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		$db->query(NULL, "
			CREATE TABLE oauth_clients (
				client_id varchar(255) NOT NULL,
				client_secret varchar(255) NOT NULL,
				redirect_uri varchar(255) NOT NULL DEFAULT '/',
				grant_types varchar(255) NOT NULL DEFAULT '',
				created int(11) unsigned NOT NULL DEFAULT '0'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		$db->query(NULL, "
			CREATE TABLE oauth_refresh_tokens (
				refresh_token varchar(255) NOT NULL,
				client_id varchar(255) NOT NULL,
				user_id int(11) unsigned DEFAULT NULL,
				expires timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
				scope varchar(255) NOT NULL DEFAULT '',
				created int(11) unsigned NOT NULL DEFAULT '0'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		$db->query(NULL, "
			ALTER TABLE oauth_access_tokens
				ADD PRIMARY KEY (access_token),
				ADD KEY fk_oauth_access_tokens_client_id (client_id),
				ADD KEY fk_oauth_access_tokens_user_id (user_id);");

		$db->query(NULL, "
			ALTER TABLE oauth_authorization_codes
				ADD PRIMARY KEY (authorization_code),
				ADD KEY fk_oauth_authorization_codes_client_id (client_id),
				ADD KEY fk_oauth_authorization_codes_user_id (user_id);");

		$db->query(NULL, "
			ALTER TABLE oauth_clients
				ADD PRIMARY KEY (client_id);");

		$db->query(NULL, "
			ALTER TABLE oauth_refresh_tokens
				ADD PRIMARY KEY (refresh_token),
				ADD KEY fk_oauth_refresh_tokens_client_id (client_id),
				ADD KEY fk_oauth_refresh_tokens_user_id (user_id);");

		$db->query(NULL, "
			ALTER TABLE oauth_access_tokens
				ADD CONSTRAINT fk_oauth_access_tokens_client_id FOREIGN KEY (client_id) REFERENCES oauth_clients (client_id) ON DELETE CASCADE,
				ADD CONSTRAINT fk_oauth_access_tokens_user_id FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE CASCADE;");

		$db->query(NULL, "
			ALTER TABLE oauth_authorization_codes
				ADD CONSTRAINT fk_oauth_authorization_codes_client_id FOREIGN KEY (client_id) REFERENCES oauth_clients (client_id) ON DELETE CASCADE,
				ADD CONSTRAINT fk_oauth_authorization_codes_user_id FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE CASCADE;");

		$db->query(NULL, "
			ALTER TABLE oauth_refresh_tokens
				ADD CONSTRAINT fk_oauth_refresh_tokens_client_id FOREIGN KEY (client_id) REFERENCES oauth_clients (client_id) ON DELETE CASCADE,
				ADD CONSTRAINT fk_oauth_refresh_tokens_user_id FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE CASCADE;");
	}

}
