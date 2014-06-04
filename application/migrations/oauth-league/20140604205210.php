<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_Oauth_league_20140604205210 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{

		$replace = URL::base(true, true) . 'user/oauth';

		$query = DB::update('oauth_client_endpoints')
			->set(array('redirect_uri' => DB::expr("REPLACE(`redirect_uri`,
				'/user/oauth','$replace')")));

		$query->execute($db);
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		// Revert
		$replace = URL::base(true, true) . 'user/oauth';

		$query = DB::update('oauth_client_endpoints')
			->set(array('redirect_uri' => '/user/oauth'))
			->where('redirect_uri', '=', $replace);

		$query->execute($db);
	}

}
