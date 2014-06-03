<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_Oauth_league_20140529030202 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$scopes = Kohana::$config->load('koauth.supported_scopes');
		$query = DB::insert('oauth_scopes')->columns(array('scope', 'name'));
		foreach ($scopes as $scope)
		{
			$query->values(array($scope, $scope));
		}
		$query->execute($db);

		$client = Kohana::$config->load('ushahidiui.oauth');
		$query = DB::insert('oauth_clients')
			->columns(array('id', 'secret', 'name'))
			->values(array($client['client'], $client['client_secret'], $client['client']));
		$query->execute($db);

		$query = DB::insert('oauth_client_endpoints')
			->columns(array('client_id', 'redirect_uri'))
			->values(array($client['client'], '/user/oauth'));
		$query->execute($db);
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$scopes = Kohana::$config->load('koauth.supported_scopes');
		DB::delete('oauth_scopes')
			->where('scope', 'IN', $scopes)
			->execute($db);

		$client = Kohana::$config->load('ushahidiui.oauth.client');
		DB::delete('oauth_clients')
			->where('id', '=', $client)
			->execute($db);

		// do not need to worry about clearing extra client data, as the tables
		// use cascading deletes.
	}

}
